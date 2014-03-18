<?php
class ITwebexperts_Vendor_Block_Listing_Listings extends Mage_Core_Block_Template {

    public function getProductCollection()
    {
        $_productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('vendor_id', array('eq' => Mage::helper('customer')->getCustomer()->getId()))
            ->load();
        return $_productCollection;
    }

}