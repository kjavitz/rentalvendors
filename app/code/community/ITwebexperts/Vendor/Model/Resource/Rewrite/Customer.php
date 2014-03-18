<?php
class ITwebexperts_Vendor_Model_Resource_Rewrite_Customer extends Mage_Customer_Model_Resource_Customer {

    protected function _getDefaultAttributes()
    {
        $defaultAttributes = parent::_getDefaultAttributes();
        $vendorAttributes = array(
            'vendor',
            'vendor_website',
            'vendor_facebook',
            'vendor_twitter',
            'vendor_google',
            'vendor_description',
            'vendor_nickname',
            'vendor_avatar',
            'vendor_is_public'
        );
        return array_merge($defaultAttributes, $vendorAttributes);
    }

}