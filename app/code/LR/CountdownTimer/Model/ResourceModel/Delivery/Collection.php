<?php

namespace LR\CountdownTimer\Model\ResourceModel\Delivery;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'delievry_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'LR\CountdownTimer\Model\Delivery',
            'LR\CountdownTimer\Model\ResourceModel\Delivery'
        );
    }
}