<?php
namespace Biztech\Productdesigner\Model;

use Magento\Framework\Model\AbstractModel;

class MediaGallery extends AbstractModel {
	public function _construct() {
		$this->_init('Biztech\Productdesigner\Model\ResourceModel\MediaGallery');
	}
}