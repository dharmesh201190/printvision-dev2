<?php
namespace Biztech\Productdesigner\Block\Adminhtml;

class Printablecolor extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'printablecolor';
        $this->_headerText = __('Manage Printable Color');
        $this->_addButtonLabel = __('Add  Printable Color');
        parent::_construct();
    }
}
