<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\Productdesigner\Model\Mysql4\Subtabs;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Biztech\Productdesigner\Model\Subtabs', 'Biztech\Productdesigner\Model\Mysql4\Subtabs');
    }

}
