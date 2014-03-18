<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$attrCode = 'vendor_avatar';

$setup->addAttribute(
    'customer',
    $attrCode,
    array(
        'type'              => 'varchar',
        'input'             => 'image',
        'label'             => 'Upload Avatar',
        'global'            => 1,
        'visible'           => 1,
        'required'          => 0,
        'user_defined'      => 1,
        'visible_on_front'  => 1,
    )
);

if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
    $customer = Mage::getModel('customer/customer');
    $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
    $setup->addAttributeToSet('customer', $attrSetId, 'General', $attrCode);
}

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', $attrCode)
        ->setData(
            'used_in_forms',
            array(
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit',
                'checkout_register'
            )
        )
        ->save();
}

$installer->endSetup();
