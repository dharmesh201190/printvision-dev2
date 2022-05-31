<?php

namespace LR\CountdownTimer\Block\Adminhtml\Delivery\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lr_countdowntimer_delivery_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Delivery Information'));
    }
}
