<?php
class ITwebexperts_Vendor_Block_Review_List extends Mage_Core_Block_Template
{

	protected $_reviewsCollection;
	
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }

    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('customer', $this->getVendor()->getId())
                ->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }
}