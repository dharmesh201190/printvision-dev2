<?php
namespace Biztech\Productdesigner\Model;

class Customerimages extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
    */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Biztech\Productdesigner\Model\Mysql4\Customerimages');
    }
}
