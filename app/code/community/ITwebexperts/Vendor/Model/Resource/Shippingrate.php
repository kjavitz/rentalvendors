<?php
class ITwebexperts_Vendor_Model_Resource_Shippingrate extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct(){
        $this->_init('vendor/shippingrate', 'id');
    }

}