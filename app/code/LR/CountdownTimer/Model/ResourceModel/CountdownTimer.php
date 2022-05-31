<?php

namespace LR\CountdownTimer\Model\ResourceModel;

class CountdownTimer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('lr_countdowntimer', 'countdowntimer_id');
    }
}