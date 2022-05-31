<?php
namespace Biztech\Productdesigner\Model\Mysql4\Designimages;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Biztech\Productdesigner\Model\Designimages', 'Biztech\Productdesigner\Model\Mysql4\Designimages');
    }

}
