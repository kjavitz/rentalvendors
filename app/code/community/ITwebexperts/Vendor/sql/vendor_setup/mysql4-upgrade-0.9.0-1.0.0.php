<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

/*$installer->run("
    ALTER TABLE {$this->getTable('sales/order')}
      ADD COLUMN `vendor_id` INT(11) NOT NULL DEFAULT 0;
");*/

$installer->endSetup();