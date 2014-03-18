<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'vendor_is_public', array(
    'label'    => 'Is vendor public',
    'visible'  => false,
    'required' => false,
));


$installer->endSetup();