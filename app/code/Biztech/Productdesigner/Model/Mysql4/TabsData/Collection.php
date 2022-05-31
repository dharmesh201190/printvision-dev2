<?php



namespace Biztech\Productdesigner\Model\Mysql4\TabsData;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

   
    protected function _construct() {
        $this->_init('Biztech\Productdesigner\Model\TabsData', 'Biztech\Productdesigner\Model\Mysql4\TabsData');
    }

}
