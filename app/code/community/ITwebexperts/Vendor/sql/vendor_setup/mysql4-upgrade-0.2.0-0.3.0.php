<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'vendor_nickname', array(
    'label'        => 'Vendor Nickname',
    'visible'      => true,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));


$installer->endSetup();
