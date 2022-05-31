<?php

namespace Biztech\Productdesigner\Model\Mysql4\Imageeffects;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'effect_id';
     protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Imageeffects', 'Biztech\Productdesigner\Model\Mysql4\Imageeffects');
    }
}
