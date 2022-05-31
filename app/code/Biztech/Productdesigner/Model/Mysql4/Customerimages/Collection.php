<?php

namespace Biztech\Productdesigner\Model\Mysql4\Customerimages;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Customerimages', 'Biztech\Productdesigner\Model\Mysql4\Customerimages');
    }
}
