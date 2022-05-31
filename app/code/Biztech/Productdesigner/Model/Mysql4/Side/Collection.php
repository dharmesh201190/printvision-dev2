<?php

namespace Biztech\Productdesigner\Model\Mysql4\Side;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
   
    protected $_idFieldName = 'imageside_id';
    protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Side', 'Biztech\Productdesigner\Model\Mysql4\Side');
    }
}
