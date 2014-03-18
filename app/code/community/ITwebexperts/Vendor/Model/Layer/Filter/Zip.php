<?php
class ITwebexperts_Vendor_Model_Layer_Filter_Zip extends Mage_Catalog_Model_Layer_Filter_Abstract {
	
	public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'zip';
    }
    
    
    public function getName()
    {
        return Mage::helper('catalog')->__('Zipcode');
    }

	
}