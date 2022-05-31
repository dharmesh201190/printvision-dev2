<?php

namespace Biztech\Productdesigner\Model\Mysql4\Designs;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

  
    protected function _construct() {
        $this->_init('Biztech\Productdesigner\Model\Designs', 'Biztech\Productdesigner\Model\Mysql4\Designs');
    }

}
