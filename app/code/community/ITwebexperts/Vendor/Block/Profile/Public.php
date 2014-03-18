<?php
class ITwebexperts_Vendor_Block_Profile_Public extends Mage_Core_Block_Template {

	protected $_reviewsCollection;

    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }
    
    public function getRatingSummary()
    {
	    $summary = false;
	    $reviewCollection = $this->getReviewsCollection();
	    $i = 0;
	    foreach ($reviewCollection AS $review) {
		    $votes = $review->getRatingVotes();
		    foreach ($votes AS $vote) {
			    $summary += $vote->getPercent();
				$i++;
		    }
	    }
	    if ($i > 0) {
		    $summary = intval($summary / $i);
	    }
	    return $summary;
    }
    
    
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('customer', $this->getVendor()->getId())
                ->setDateOrder()
                ->addRateVotes();
        }
        return $this->_reviewsCollection;
    }

}