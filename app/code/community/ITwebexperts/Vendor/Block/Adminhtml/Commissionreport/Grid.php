<?php
class ITwebexperts_Vendor_Block_Adminhtml_Commissionreport_Grid extends Mage_Adminhtml_Block_Report_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridVendorCommissionreport');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('vendor/reports_commissionreport_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => $this->__('Vendor Name'),
            'sortable'  => false,
            'index'     => 'vendor_email'
        ));

        $this->addColumn('orders_count', array(
            'header'    => $this->__('Number of Orders'),
            'width'     => '100px',
            'sortable'  => false,
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        $baseCurrencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('orders_avg_amount', array(
            'header'    => $this->__('Paid'),
            'width'     => '200px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'total'     => 'orders_sum_amount/orders_count',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('orders_sum_amount', array(
            'header'    => $this->__('Commission'),
            'width'     => '200px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ));

        $this->addExportType('*/*/exportCommissionCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCommissionExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}