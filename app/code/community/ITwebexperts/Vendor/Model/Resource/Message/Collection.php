<?php
class ITwebexperts_Vendor_Model_Resource_Message_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct(){
        parent::_construct();
        $this->_init('vendor/message');

    }

}