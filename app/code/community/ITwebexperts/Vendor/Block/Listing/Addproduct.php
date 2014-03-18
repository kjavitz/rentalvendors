<?php
class ITwebexperts_Vendor_Block_Listing_Addproduct extends Mage_Core_Block_Template {

    public function getStoreCategories()
    {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }

    public function getChildCategories($category)
    {
        $flatHelper = Mage::helper('catalog/category_flat');
        if ($flatHelper->isAvailable() && $flatHelper->isBuilt(true)) {
            $children = (array)$category->getChildrenNodes();
        } else {
            $children = $category->getChildren();
        }
        return $children;
    }

    public function getProduct()
    {
        return Mage::registry('vendor_current_product');
    }

    public function getReservations()
    {
        if ($this->getProduct()) {
            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            $reservationOrderCollection = Mage::getModel('payperrentals/reservationorders')
                ->getCollection()
                ->addProductIdFilter($this->getProduct()->getId())
                ->addFieldToFilter('end_date', array('gt' => $todayStartOfDayDate))
                ->load();
            return $reservationOrderCollection;
        } else {
            return array();
        }
    }

    public function getBlackoutDates()
    {
        if ($this->getProduct()) {
            $blackoutDatesCollection = Mage::getModel('payperrentals/blackoutdates')
                ->getCollection()
                ->addProductIdFilter($this->getProduct()->getId())
                ->load();
            return $blackoutDatesCollection;
        } else {
            return array();
        }
    }

}