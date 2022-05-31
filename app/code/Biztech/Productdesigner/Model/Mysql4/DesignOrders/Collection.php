<?php


namespace Biztech\Productdesigner\Model\Mysql4\DesignOrders;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Biztech\Productdesigner\Model\DesignOrders', 'Biztech\Productdesigner\Model\Mysql4\DesignOrders');
    }

}
