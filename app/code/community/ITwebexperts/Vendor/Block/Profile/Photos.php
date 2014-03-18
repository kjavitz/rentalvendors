<?php
class ITwebexperts_Vendor_Block_Profile_Photos extends Mage_Core_Block_Template {

    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }

    public function getGallery()
    {
        $_gallery = Mage::getModel('vendor/gallery')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => $this->getVendor()->getId()));
        return $_gallery;
    }

}