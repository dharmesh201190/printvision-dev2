<?php
namespace Biztech\Productdesigner\Model;
    class Selectionarea extends \Magento\Framework\Model\AbstractModel
    {
        

        protected function _construct()
    {
        parent::_construct();
        $this->_init('Biztech\Productdesigner\Model\Mysql4\Selectionarea');
    }
}
