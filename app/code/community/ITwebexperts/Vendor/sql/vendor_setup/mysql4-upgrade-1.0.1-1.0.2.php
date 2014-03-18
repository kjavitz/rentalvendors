<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->run("
    CREATE TABLE {$this->getTable('vendor/message')} (
      `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
      `from_id` int(10) unsigned NOT NULL COMMENT 'Customer Id',
      `to_id` int(10) unsigned NOT NULL COMMENT 'Vendor Id',
      `order_item_id` int(10) unsigned NOT NULL COMMENT 'Order Item Id',
      `comment` text COMMENT 'Comment',
      `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
      PRIMARY KEY (`entity_id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vendor Nessages' AUTO_INCREMENT=1;
");

$installer->endSetup();