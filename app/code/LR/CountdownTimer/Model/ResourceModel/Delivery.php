<?php

namespace LR\CountdownTimer\Model\ResourceModel;

class Delivery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('lr_delivery', 'delivery_id');
    }
}