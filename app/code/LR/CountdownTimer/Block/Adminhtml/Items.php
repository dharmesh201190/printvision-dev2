<?php

namespace LR\CountdownTimer\Block\Adminhtml;

class Items extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'items';
        $this->_headerText = __('Manage Holidays');
        $this->_addButtonLabel = __('Add New Holiday');
        parent::_construct();
    }
}
