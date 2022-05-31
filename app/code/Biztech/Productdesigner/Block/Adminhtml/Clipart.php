<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

class Clipart extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'clipart';
        $this->_headerText = __('Manage Clipart Categories');
        $this->_addButtonLabel = __('Add  Clipart Category');
        parent::_construct();
    }
}
