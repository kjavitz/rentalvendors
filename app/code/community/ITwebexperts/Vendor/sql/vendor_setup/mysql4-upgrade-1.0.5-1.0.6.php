<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer_address', 'is_vendor', array(
    'label'    => 'Is vendor address',
    'visible'  => false,
    'required' => false,
));


$installer->endSetup();
