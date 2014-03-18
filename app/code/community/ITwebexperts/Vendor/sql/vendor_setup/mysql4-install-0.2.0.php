<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'vendor', array(
    'label'    => 'Is vendor',
    'visible'  => false,
    'required' => false,
));

$setup->addAttribute('customer', 'vendor_website', array(
    'label'        => 'Website URL',
    'visible'      => true,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));

$setup->addAttribute('customer', 'vendor_facebook', array(
    'label'        => 'Facebook Page',
    'visible'      => true,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));

$setup->addAttribute('customer', 'vendor_twitter', array(
    'label'        => 'Twitter Page',
    'visible'      => true,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));

$setup->addAttribute('customer', 'vendor_google', array(
    'label'        => 'Google + Page',
    'visible'      => true,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));

$setup->addAttribute('customer', 'vendor_description', array(
    'label'        => 'Description',
    'visible'      => true,
    'required'     => false,
    'type'         => 'text',
    'input'        => 'text',
));

$installer->endSetup();
