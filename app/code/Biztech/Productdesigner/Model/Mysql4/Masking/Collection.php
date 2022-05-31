<?php

namespace Biztech\Productdesigner\Model\Mysql4\Masking;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
   
    protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Masking', 'Biztech\Productdesigner\Model\Mysql4\Masking');
    }
}
