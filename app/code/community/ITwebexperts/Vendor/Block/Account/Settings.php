<?php
class ITwebexperts_Vendor_Block_Account_Settings extends Mage_Customer_Block_Account_Dashboard {

    public function getDefaultVendorAddress()
    {
    	$_defaultVendorAddress = false;
	    $_vendor = $this->getVendor();
	    if ($_vendor->getDefaultVendor()) {
		    $_addressCollection = $_vendor->getAddressesCollection();
		    $_addressCollection->clear();
		    foreach ($_addressCollection AS $_address) {
		    	
			    if ($_address->getId() == $_vendor->getDefaultVendor()) {
				    $_defaultVendorAddress = $_address;
			    }
		    }
	    }
	    
	    
	    return $_defaultVendorAddress;
    }
    
    public function getAdditionalAddresses()
    {
    	$_vendor = $this->getVendor();
    	$_addressCollection = $_vendor->getAddressesCollection()->clear();
    	$_addressCollection->addAttributeToFilter('is_vendor', array('eq' => 1));
    	if ($_vendor->getDefaultVendor()) {
	    	$_addressCollection->addFieldToFilter('entity_id', array('neq' => $_vendor->getDefaultVendor()));
    	}
	    return $_addressCollection;
    }

	public function getVendor()
	{
		return $this->helper('customer')->getCustomer();
	}
}