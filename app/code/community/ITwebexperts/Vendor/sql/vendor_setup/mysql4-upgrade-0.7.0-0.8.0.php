<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_id', array(
    'group'			=> 'Vendor',
    'label'         => 'Vendor ID',
    'type'			=> 'int',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => 0,
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  60,
));




$entityTypeId = $setup->getEntityTypeId('catalog_product');
$select = $this->_conn->select()
    ->from($this->getTable('eav/attribute_set'))
    ->where('entity_type_id = :entity_type_id');
$sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));

foreach ($sets as $set) {
    $setup->addAttributeGroup('catalog_product', $set['attribute_set_id'], 'Vendor');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Vendor', 'vendor_style');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Vendor', 'vendor_color');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Vendor', 'vendor_product_spec');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Vendor', 'vendor_rental_terms');
}


$installer->endSetup();