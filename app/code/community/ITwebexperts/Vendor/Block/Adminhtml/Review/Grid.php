<?php
class ITwebexperts_Vendor_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	public function __construct()
    {
        parent::__construct();
        $this->setId('vendorReviwGrid');
        $this->setDefaultSort('created_at');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('review/review');
        $collection = $model->getProductCollection();

        if ($this->getProductId() || $this->getRequest()->getParam('productId', false)) {
            $productId = $this->getProductId();
            if (!$productId) {
                $productId = $this->getRequest()->getParam('productId');
            }
            $this->setProductId($productId);
            $collection->addEntityFilter($this->getProductId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId){
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if (Mage::registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }
        
        $collection->getSelect()
            ->where('rt.entity_id = ?', 2);
        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('review_id', array(
            'header'        => Mage::helper('review')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'rt.review_id',
            'index'         => 'review_id',
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('review')->__('Created On'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'filter_index'  => 'rt.created_at',
            'index'         => 'review_created_at',
        ));

        if( !Mage::registry('usePendingFilter') ) {
            $this->addColumn('status', array(
                'header'        => Mage::helper('review')->__('Status'),
                'align'         => 'left',
                'type'          => 'options',
                'options'       => Mage::helper('review')->getReviewStatuses(),
                'width'         => '100px',
                'filter_index'  => 'rt.status_id',
                'index'         => 'status_id',
            ));
        }

        $this->addColumn('title', array(
            'header'        => Mage::helper('review')->__('Title'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.title',
            'index'         => 'title',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('nickname', array(
            'header'        => Mage::helper('review')->__('Nickname'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.nickname',
            'index'         => 'nickname',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('detail', array(
            'header'        => Mage::helper('review')->__('Review'),
            'align'         => 'left',
            'index'         => 'detail',
            'filter_index'  => 'rdt.detail',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => Mage::helper('review')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
            ));
        }

        $this->addColumn('type', array(
            'header'    => Mage::helper('review')->__('Type'),
            'type'      => 'select',
            'index'     => 'type',
            'filter'    => 'adminhtml/review_grid_filter_type',
            'renderer'  => 'adminhtml/review_grid_renderer_type'
        ));



        $this->addColumn('action',
            array(
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getReviewId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/catalog_product_review/edit',
                            'params'=> array(
                                'productId' => $this->getProductId(),
                                'customerId' => $this->getCustomerId(),
                                'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null
                            )
                         ),
                         'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        $this->addRssList('rss/catalog/review', Mage::helper('catalog')->__('Pending Reviews RSS'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('review')->__('Delete'),
            'url'  => $this->getUrl(
                '*/*/massDelete',
                array('ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'confirm' => Mage::helper('review')->__('Are you sure?')
        ));

        $statuses = Mage::helper('review')->getReviewStatusesOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('update_status', array(
            'label'         => Mage::helper('review')->__('Update Status'),
            'url'           => $this->getUrl(
                '*/*/massUpdateStatus',
                array('ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'additional'    => array(
                'status'    => array(
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('review')->__('Status'),
                    'values'    => $statuses
                )
            )
        ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_review/edit', array(
            'id' => $row->getReviewId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null,
        ));
    }

    public function getGridUrl()
    {
        if( $this->getProductId() || $this->getCustomerId() ) {
            return $this->getUrl(
                '*/adminhtml_review/' . (Mage::registry('usePendingFilter') ? 'pending' : ''),
                array(
                    'productId' => $this->getProductId(),
                    'customerId' => $this->getCustomerId(),
                )
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
	
}