<?php
class ITwebexperts_Vendor_Block_Listing_Reservations extends Mage_Core_Block_Template {
	
	protected $_orderCollection;
	protected $_itemsCollection;
	
    public function getOrderCollection()
    {
    	if (is_null($this->_orderCollection)) {
	    	$this->_orderCollection = Mage::getModel('sales/order')
	            ->getCollection()
	            ->addFieldToSelect('*')
	            ->addFieldToFilter('vendor_id', array('eq' => Mage::helper('customer')->getCustomer()->getId()))
	            ->setOrder('entity_id', 'DESC');
	        
    	}
        return $this->_orderCollection;
    }
    
    public function getItemsCollection()
    {
	    if (is_null($this->_itemsCollection)) {
	    	$_orderIds = $this->getOrderCollection()->getAllIds();
	    	$this->_itemsCollection = Mage::getModel('sales/order_item')->getCollection()
	    		->addFieldToSelect('*')
	    		->addFieldToFilter('order_id', array('in' => $_orderIds));
	    	
	    	if (isset($_GET['start_turnover_before']) && $_GET['start_turnover_before']) {
		        $this->_itemsCollection->addFieldToFilter('start_turnover_before', array('gteq' => date('Y-m-d H:i:s', strtotime($_GET['start_turnover_before']))));
	        }
	        if (isset($_GET['end_turnover_after']) && $_GET['end_turnover_after']) {
		        $this->_itemsCollection->addFieldToFilter('end_turnover_after', array('lteq' => date('Y-m-d H:i:s', strtotime($_GET['end_turnover_after']))));

	        }
	    		
	    	$orderBy = 'order_id';
	    	$orderDir = 'DESC';
	        if (isset($_GET['order']) && $_GET['order']) {
		        $orderBy = $_GET['order'];
	        }
	        if (isset($_GET['dir']) && $_GET['dir']) {
		        $orderDir = $_GET['dir'];
	        }
	        $this->_itemsCollection->setOrder($orderBy, $orderDir);
	    }
	    return $this->_itemsCollection;
    }
    
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'vendor.listing.reservations.pager')
            ->setCollection($this->getItemsCollection());
        $this->setChild('pager', $pager);
        $this->getItemsCollection()->load();
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}