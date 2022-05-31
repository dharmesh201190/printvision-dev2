<?php

/**
 * Copyright © 2015 Biztech. All rights reserved.
 */

namespace Biztech\Productdesigner\Model;

class DesignOrders extends \Magento\Framework\Model\AbstractModel {

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('Biztech\Productdesigner\Model\Mysql4\DesignOrders');
    }

}
