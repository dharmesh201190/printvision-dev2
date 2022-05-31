<?php
namespace Biztech\Productdesigner\Model\Mysql4\Clipartmedia;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected function _construct()
    {
        $this->_init('Biztech\Productdesigner\Model\Clipartmedia', 'Biztech\Productdesigner\Model\Mysql4\Clipartmedia');
    }
}
