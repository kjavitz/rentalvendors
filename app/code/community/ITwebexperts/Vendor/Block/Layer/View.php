<?php
class ITwebexperts_Vendor_Block_Layer_View extends Mage_Catalog_Block_Layer_View {
	
	protected $_zipFilterBlockName;
	
	protected function _initBlocks()
    {
    	parent::_initBlocks();
    	$this->_zipFilterBlockName = 'vendor/layer_filter_zip';
    }
    
    
    
    protected function _prepareLayout()
    {
        /*$stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
            ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
            ->setLayer($this->getLayer())
            ->init();*/
            
        $zipBlock = $this->getLayout()->createBlock($this->_zipFilterBlockName)
            ->setLayer($this->getLayer())
            ->init();


        /*$this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);*/
        $this->setChild('zip_filter', $zipBlock);

        /*$filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = $this->_priceFilterBlockName;
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = $this->_decimalFilterBlockName;
            } else {
                $filterBlockName = $this->_attributeFilterBlockName;
            }

            $this->setChild($attribute->getAttributeCode() . '_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init());
        }*/

        //$this->getLayer()->apply();

        return parent::_prepareLayout();
    }
    
    protected function _getZipFilter()
    {
	    return $this->getChild('zip_filter');
    }
    
    
    public function getFilters()
    {
        $filters = parent::getFilters();
        if ($zipFilter = $this->_getZipFilter()) {
            $filters[] = $zipFilter;
        }

        return $filters;
    }

	
}