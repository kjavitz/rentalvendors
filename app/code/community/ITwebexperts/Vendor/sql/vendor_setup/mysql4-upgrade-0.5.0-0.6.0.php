<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('vendor/gallery')};

    CREATE TABLE {$this->getTable('vendor/gallery')} (
        `id` INT( 11 ) NOT NULL auto_increment,
        `customer_id` INT(10) unsigned NOT NULL DEFAULT '0',
        `path` VARCHAR(250) NOT NULL DEFAULT '',
        `link_url` VARCHAR(250) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`),
        KEY `IDX_VENDOR_GALLERY_CUSTOMER_ID` (`customer_id`)
    ) DEFAULT CHARSET utf8 ENGINE = InnoDB;

    ALTER TABLE {$this->getTable('vendor/gallery')}
      ADD CONSTRAINT `FK_VENDOR_GALLERY_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID` FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();