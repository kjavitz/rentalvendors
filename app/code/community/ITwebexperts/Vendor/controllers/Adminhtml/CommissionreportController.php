<?php
class ITwebexperts_Vendor_Adminhtml_CommissionreportController extends Mage_Adminhtml_Controller_Report_Abstract {

    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('vendor')->__('Vendor'), Mage::helper('payperrentals')->__('Commission'));
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Reports'))->_title($this->__('Vendor'))->_title($this->__('Commission'));
        $this->_initAction()
            ->_setActiveMenu('vendor/reports/commissionreport')
            ->_addBreadcrumb(Mage::helper('vendor')->__('Commission Report'), Mage::helper('payperrentals')->__('Commission Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_commissionreport.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }


    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('report_from', 'report_to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }
    
    /**
     * Export Rent Products report to CSV format action
     *
     */
    public function exportCommissionCsvAction()
    {
        $_fileName = 'products_rented.csv';
        $_content = $this->getLayout()
            ->createBlock('vendor/adminhtml_commissionreport_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($_fileName, $_content);
    }

    /**
     * Export Rent Products report to XML format action
     *
     */
    public function exportCommissionExcelAction()
    {
        $_fileName = 'products_rented.xml';
        $_content = $this->getLayout()
            ->createBlock('vendor/adminhtml_commissionreport_grid')
            ->getExcel($_fileName);

        $this->_prepareDownloadResponse($_fileName, $_content);
    }

}