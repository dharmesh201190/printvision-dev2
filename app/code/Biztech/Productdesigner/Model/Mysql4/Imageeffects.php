<?php

namespace Biztech\Productdesigner\Model\Mysql4;
class Imageeffects extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {    
        $this->_init('productdesigner_imageeffects', 'effect_id');
    }
}
