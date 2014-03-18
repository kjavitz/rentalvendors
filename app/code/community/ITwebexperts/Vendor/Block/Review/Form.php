<?php
class ITwebexperts_Vendor_Block_Review_Form extends Mage_Review_Block_Form {
	
	public function getRatings()
    {
        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('customer')
            ->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
    
    public function getAction()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('vendor/profile/postReview', array('id' => $this->getVendor()->getId()));
    }
    
    
    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }
	
}