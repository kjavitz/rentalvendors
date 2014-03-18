<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('sales/order')}
      ADD COLUMN `vendor_comission_amount` decimal(12,4) DEFAULT NULL;
");

$installer->endSetup();