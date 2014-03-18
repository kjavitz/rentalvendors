<?php
class ITwebexperts_Vendor_Model_Resource_Reports_Commissionreport_Collection extends Mage_Reports_Model_Mysql4_Customer_Orders_Collection {

    protected function _joinFields($from = '', $to = '')
    {

        $this->getSelect()->joinInner(
            array('customer' => $this->getTable('customer/entity')),
            'main_table.vendor_id = customer.entity_id',
            array(
                'vendor_email' => 'customer.email'
            ))
        ->where('main_table.vendor_id IS NOT NULL')
        ->group('main_table.vendor_id');

        $this->addOrdersCount()
            ->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to, 'datetime' => true));


        return $this;
    }

    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('main_table.store_id', array('in' => (array)$storeIds));
            $this->addSumAvgTotals(1)
                ->orderByOrdersCount();
        } else {
            $this->addSumAvgTotals()
                ->orderByOrdersCount();
        }

        return $this;
    }


    public function addSumAvgTotals($storeId = 0)
    {
        $adapter = $this->getConnection();
        $baseSubtotalRefunded = $adapter->getIfNullSql('main_table.base_subtotal_refunded', 0);
        $baseSubtotalCanceled = $adapter->getIfNullSql('main_table.base_subtotal_canceled', 0);
        $baseDiscountCanceled = $adapter->getIfNullSql('main_table.base_discount_canceled', 0);

        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? "(main_table.base_subtotal -
            {$baseSubtotalRefunded} - {$baseSubtotalCanceled} - ABS(main_table.base_discount_amount) -
            {$baseDiscountCanceled}) * main_table.base_to_global_rate"
            : "main_table.base_subtotal - {$baseSubtotalCanceled} - {$baseSubtotalRefunded} -
            ABS(main_table.base_discount_amount) - {$baseDiscountCanceled}";

        $this->getSelect()
            ->columns(array('orders_avg_amount' => "SUM({$expr}) - SUM(vendor_comission_amount)"))
            ->columns(array('orders_sum_amount' => "SUM(vendor_comission_amount)"));
        
        

        return $this;
    }

    public function calculateSales($isFilter = 0)
    {
        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $adapter = $this->getConnection();

        if (Mage::getStoreConfig('sales/dashboard/use_aggregated_data')) {
            $this->setMainTable('sales/order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $adapter->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                0);
            $this->getSelect()->columns(array(
                'lifetime' => 'SUM(main_table.total_revenue_amount)',
                'average'  => $averageExpr
            ));

            if (!$isFilter) {
                $this->addFieldToFilter('main_table.store_id',
                    array('eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId())
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        } else {
            $this->setMainTable('sales/order');
            $this->removeAllFieldsFromSelect();

            $expr = $this->_getSalesAmountExpression();

            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }

            $this->getSelect()
                ->columns(array(
                    'lifetime' => "SUM({$expr})",
                    'average'  => "AVG({$expr})"
                ))
                ->where('main_table.status NOT IN(?)', $statuses)
                ->where('main_table.state NOT IN(?)', array(
                        Mage_Sales_Model_Order::STATE_NEW,
                        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
                );
        }
        return $this;
    }
}