<?php
class ITwebexperts_Vendor_Block_Adminhtml_Review_Main extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct()
    {
        $this->_addButtonLabel = Mage::helper('review')->__('Add New Review');
        parent::__construct();
        $this->_removeButton('add');
		
		$this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerName = $this->escapeHtml($customerName);
        }
        $productId = $this->getRequest()->getParam('productId', false);
        $productName = null;
        if ($productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productName =  $this->escapeHtml($product->getName());
        }

        if( Mage::registry('usePendingFilter') === true ) {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews of Customer `%s`', $customerName);
            } else {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('All Reviews of Customer `%s`', $customerName);
            } elseif ($productName) {
                $this->_headerText = Mage::helper('review')->__('All Reviews of Product `%s`', $productName);
            } else {
                $this->_headerText = Mage::helper('review')->__('All Reviews');
            }
        }
    }
	
}