<?php
class ITwebexperts_Vendor_Model_Resource_Gallery extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct(){
        $this->_init('vendor/gallery', 'id');
    }

}