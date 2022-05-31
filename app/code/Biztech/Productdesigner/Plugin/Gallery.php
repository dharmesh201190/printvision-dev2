<?php


namespace Biztech\Productdesigner\Plugin;

class Gallery {

	public function afterCreateBatchBaseSelect(\Magento\Catalog\Model\ResourceModel\Product\Gallery $subject, \Magento\Framework\DB\Select $select) {
		$select->joinLeft(
			['image_side' => $subject->getTable('catalog_product_entity_media_gallery_value')],
			implode(
                ' AND ',
                [
                    'main.value_id = image_side.value_id',
                    $subject->getConnection()->quoteInto('image_side.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ]
            ),
			['image_side_default' => 'image_side']
		);
        return $select;
    }
}