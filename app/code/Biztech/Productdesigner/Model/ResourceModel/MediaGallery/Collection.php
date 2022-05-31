<?php
namespace Biztech\Productdesigner\Model\ResourceModel\MediaGallery;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection {
	public function _construct() {
		$this->_init('Biztech\Productdesigner\Model\MediaGallery', 'Biztech\Productdesigner\Model\ResourceModel\MediaGallery');
	}
}
