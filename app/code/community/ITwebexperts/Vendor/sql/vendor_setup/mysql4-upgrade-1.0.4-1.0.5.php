<?php

$installer = $this;

$data = array(
    Mage_Review_Model_Review::ENTITY_CUSTOMER_CODE       => array(
        array(
            'rating_code'   => 'Feedback',
            'position'      => 0
        )
    )
);

foreach ($data as $entityCode => $ratings) {
    //Fill table rating/rating_entity
    $installer->getConnection()
        ->insert($installer->getTable('rating_entity'), array('entity_code' => $entityCode));
    $entityId = $installer->getConnection()->lastInsertId($installer->getTable('rating_entity'));
	
	$ratingStores = array();
	$allStore = Mage::getModel('core/store')->getCollection();
    foreach ($ratings as $bind) {
        //Fill table rating/rating
        $bind['entity_id'] = $entityId;
        $installer->getConnection()->insert($installer->getTable('rating'), $bind);

        //Fill table rating/rating_option
        $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('rating'));
        $optionData = array();
        for ($i = 1; $i <= 5; $i ++) {
            $optionData[] = array(
                'rating_id' => $ratingId,
                'code'      => (string)$i,
                'value'     => $i,
                'position'  => $i
            );
        }
        $installer->getConnection()->insertMultiple($installer->getTable('rating_option'), $optionData);
        foreach ($allStore AS $store) {
	        //if ($store->getId() != 0) {
		        $ratingStores[] = array(
		        	'rating_id' => $ratingId,
		        	'store_id' => $store->getId()
		        );
	        //}
        }
    }
    $installer->getConnection()->insertMultiple($installer->getTable('rating_store'), $ratingStores);
}
