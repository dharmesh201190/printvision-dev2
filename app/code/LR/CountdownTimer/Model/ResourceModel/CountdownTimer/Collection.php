<?php

namespace LR\CountdownTimer\Model\ResourceModel\CountdownTimer;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'countdowntimer_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'LR\CountdownTimer\Model\CountdownTimer',
            'LR\CountdownTimer\Model\ResourceModel\CountdownTimer'
        );
    }
}