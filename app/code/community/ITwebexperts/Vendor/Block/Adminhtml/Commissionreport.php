<?php
class ITwebexperts_Vendor_Block_Adminhtml_Commissionreport extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_controller = 'adminhtml_commissionreport';
        $this->_blockGroup = 'vendor';
        $this->_headerText = Mage::helper('reports')->__('Commission Report');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'gridVendorCommissionreportJsObject.doFilter()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }

}