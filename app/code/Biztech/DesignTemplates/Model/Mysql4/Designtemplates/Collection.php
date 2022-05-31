<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Model\Mysql4\Designtemplates;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
     protected $_idFieldName = 'designtemplates_id';
     
    protected function _construct()
    {
        $this->_init('Biztech\DesignTemplates\Model\Designtemplates', 'Biztech\DesignTemplates\Model\Mysql4\Designtemplates');
    }
}
