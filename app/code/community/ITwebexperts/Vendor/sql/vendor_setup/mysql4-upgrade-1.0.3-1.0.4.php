<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();



$installer->run("
    CREATE TABLE {$this->getTable('vendor/shippingrate')} (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
		`website_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Website Id',
		`customer_id` int(10) unsigned NOT NULL DEFAULT '0',
		`dest_zip_codes` varchar(10) NOT NULL DEFAULT '*' COMMENT 'Destination Post Code (Zip)',
		`rate_name` varchar(20) NOT NULL COMMENT 'Rate Condition name',
		`price` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Price',
		`is_pickup` BOOL NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vendor Shpping' AUTO_INCREMENT=1;
");

$installer->endSetup();