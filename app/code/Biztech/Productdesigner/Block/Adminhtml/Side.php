<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

class Side extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'side';
        $this->_headerText = __('Image Side');
        $this->_addButtonLabel = __('Add  Image Side');
        parent::_construct();
    }
}
