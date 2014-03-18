<?php
class ITwebexperts_Vendor_Block_Layer_Filter_Zip extends Mage_Catalog_Block_Layer_Filter_Abstract {
	
	public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'vendor/layer_filter_zip';
        $this->setTemplate('vendor/layer/filter/zip.phtml');
    }
    
    public function getItemsCount()
    {
	    return 1;
    }
	
}