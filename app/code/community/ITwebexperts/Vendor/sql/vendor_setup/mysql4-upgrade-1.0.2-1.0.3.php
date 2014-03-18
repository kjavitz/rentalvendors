<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();



$installer->run("
    CREATE TABLE {$this->getTable('vendor/postcode')} (
		`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
		`zip` VARCHAR(20) NOT NULL DEFAULT '',
		`type` VARCHAR(50) NOT NULL DEFAULT '',
		`primary_city` VARCHAR(250) NOT NULL DEFAULT '',
		`acceptable_cities` VARCHAR(250) NOT NULL DEFAULT '',
		`unacceptable_cities` VARCHAR(250) NOT NULL DEFAULT '',
		`state` VARCHAR(50) NOT NULL DEFAULT '',
		`county` VARCHAR(250) NOT NULL DEFAULT '',
		`timezone` VARCHAR(250) NOT NULL DEFAULT '',
		`area_codes` VARCHAR(250) NOT NULL DEFAULT '',
		`latitude` decimal(12,4) DEFAULT NULL,
		`longitude` decimal(12,4) DEFAULT NULL,
		`world_region` VARCHAR(250) NOT NULL DEFAULT '',
		`country` VARCHAR(250) NOT NULL DEFAULT '',
		`decommissioned` VARCHAR(250) NOT NULL DEFAULT '',
		`estimated_population` VARCHAR(250) NOT NULL DEFAULT '',
		`notes` text COMMENT 'Comment',
      PRIMARY KEY (`entity_id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vendor Nessages' AUTO_INCREMENT=1;
");

$installer->endSetup();