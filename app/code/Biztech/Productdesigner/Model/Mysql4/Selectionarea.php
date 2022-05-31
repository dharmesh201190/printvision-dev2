<?php

namespace Biztech\Productdesigner\Model\Mysql4;
class Selectionarea extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {    
        $this->_init('productdesigner_image_selection_area', 'design_area_id');
    }
}
