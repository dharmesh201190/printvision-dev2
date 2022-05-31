<?php

namespace Biztech\Productdesigner\Model\Mysql4\Selectionarea;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
     protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Selectionarea', 'Biztech\Productdesigner\Model\Mysql4\Selectionarea');
    }
}
