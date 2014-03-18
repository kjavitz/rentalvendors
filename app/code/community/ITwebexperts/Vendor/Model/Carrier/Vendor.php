<?php
class ITwebexperts_Vendor_Model_Carrier_Vendor extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface {

	protected $_code = 'vendor';
    protected $_isFixed = true;
    
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');
        
        $vendorIds = array();
        
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
            	$product = $item->getProduct();
            	if (!isset($vendorIds[$product->getVendorId()])) {
	            	$vendorIds[$product->getVendorId()] = $item->getQty();
            	} else {
	            	$vendorIds[$product->getVendorId()] += $item->getQty();
            	}
            }
        }
        
        
        
        foreach ($vendorIds AS $vendorId => $qty) {
	        $shippingCollection = Mage::getModel('vendor/shippingrate')->getCollection()->addFieldToFilter('customer_id', array('eq' => $vendorId));
	        $vendor = Mage::getModel('customer/customer')->load($vendorId);
	        if ($vendor->getId()) {
		        foreach ($shippingCollection AS $shipping) {
		        	echo 
					$method = Mage::getModel('shipping/rate_result_method');
	
		            $method->setCarrier('vendor_'.$vendor->getId());
		            $method->setCarrierTitle($vendor->getVendorNickname());
		
		            $method->setMethod('vendor_'.$vendor->getId().'_'.$shipping->getId());
		            $method->setMethodTitle($shipping->getRateName());
		            if ($shipping->getIsPerOrder() == 0) {
			            $method->setPrice($shipping->getPrice() * $qty);
						$method->setCost($shipping->getPrice() * $qty);
		            } else {
			            $method->setPrice($shipping->getPrice());
						$method->setCost($shipping->getPrice());
		            }
		            $result->append($method);
				}
	        }
			
        }
        
        

        return $result;
    }
    
    public function getAllowedMethods()
    {
        return array('flatrate'=>$this->getConfigData('name'));
    }
	   
}