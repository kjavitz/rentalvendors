<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'default_vendor', array(
    'label'    => 'Default vendor address id',
    'visible'  => false,
    'required' => false,
));



$installer->endSetup();
