<?php
namespace Biztech\Productdesigner\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MediaGallery extends AbstractDb {

	public function _construct() {
		/* Custom Table Name */
		$this->_init('catalog_product_entity_media_gallery_value', 'value_id');
	}
}