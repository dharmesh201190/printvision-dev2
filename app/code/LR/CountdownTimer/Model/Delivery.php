<?php

namespace LR\CountdownTimer\Model;

use Magento\Framework\Model\AbstractModel;

class Delivery extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('LR\CountdownTimer\Model\ResourceModel\Delivery');
    }
}