<?php

namespace LR\CountdownTimer\Block\Adminhtml;

class Delivery extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'delivery';
        $this->_headerText = __('Manage Delivery');
        $this->_addButtonLabel = __('Add New Delivery');
        parent::_construct();
    }
}
