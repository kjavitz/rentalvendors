<?php
class ITwebexperts_Vendor_Model_Rewrite_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {
	
	
	public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        
        $shippingMethod = explode('|', $shippingMethod);
        foreach ($shippingMethod AS $method) {
	        if ($method) {
		        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($method);
		        if (!$rate) {
		            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
		        }
	        }
        }
        Mage::getSingleton('checkout/session')->setVShippingMethod(implode('|', $shippingMethod));
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod(implode('|', $shippingMethod));

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }
	
    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }
        
         if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
                $this->getQuote()->getCustomer()->save();
                $this->getCustomerSession()->loginById($this->getQuote()->getCustomer()->getId());
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        

        
        

        $vendorIds = array();
        $quoteId = $this->getQuote()->getId();
        foreach ($this->getQuote()->getAllItems() AS $item) {
            $vendorIds[] = $item->getProduct()->getVendorId();
            $vendorIds[] = $item->getProduct()->getVendorId();
        }
        $vendorIds = array_unique($vendorIds);

        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();
        foreach ($shippingAddresses as $address) {
            foreach ($vendorIds AS $vendorId) {
                $order = $this->_prepareOrder( clone $address, $vendorId);
                if ($isNewCustomer) {
                	$order->setCustomerId($this->getQuote()->getCustomer()->getId());
                }
                $comissionRate = (int)Mage::getStoreConfig('vendor/general/comission');
                if ($comissionRate) {
                    $order->setVendorComissionAmount($order->getSubtotal() / 100 * $comissionRate);
                }
                $order->setVendorId($vendorId);
                $order->setStatus('pending');
                $order->setState('pending');
                $order->save();
                $order->place();
                $orders[] = $order;
            }
        }
        
       
        
        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($quoteId)
            ->clearHelperData();


        Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));

        $this->_checkoutSession->setLastOrderId($order->getId())
            ->setLastSuccessQuoteId($quoteId)
            ->setLastRealOrderId($order->getIncrementId());

        $this->getQuote()->setIsActive(false)->save();

		

        return $this;
    }


    /**
     * Prepare order based on quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Order
     * @throws  Mage_Checkout_Exception
     */
    protected function _prepareOrder(Mage_Sales_Model_Quote_Address $address, $vendorId = false)
    {
        $quote = $this->getQuote();
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
        $quote->collectTotals();

        $convertQuote = Mage::getSingleton('sales/convert_quote');

        $address->setSubtotal(0);
        $address->setBaseSubtotal(0);

        $address->setGrandTotal(0);
        $address->setBaseGrandTotal(0);
        $excludeSubtotal = 0;
        foreach ($address->getAllItems() as $item) {
            if ($vendorId) {
                if ($item->getProduct()->getVendorId() != $vendorId) {
                    $excludeSubtotal += $item->getRowTotal();
                }
            }
        }

        $address->collectTotals();

        $address->setSubtotal($address->getSubtotal() - $excludeSubtotal);
        $address->setBaseSubtotal($address->getBaseSubtotal() - $excludeSubtotal);

        $address->setGrandTotal($address->getGrandTotal() - $excludeSubtotal);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $excludeSubtotal);


        $order = $convertQuote->addressToOrder($address);
        $order->setQuote($quote);
        $order->setBillingAddress(
            $convertQuote->addressToOrderAddress($quote->getBillingAddress())
        );


        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        } else {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
        }

        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
        if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
            $order->getPayment()->setMethod('free');
        }



        foreach ($quote->getAllItems() as $item) {
            $_quoteItem = $item->getQuoteItem();
            if (!$_quoteItem) {
                $_quoteItem = $item;
            }
            if ($vendorId) {
                if ($_quoteItem->getProduct()->getVendorId() != $vendorId) {
                    continue;
                }
            }
            $item->setProductType($_quoteItem->getProductType())
                ->setProductOptions(
                    $_quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($_quoteItem->getProduct())
                );
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }
        return $order;
    }

}