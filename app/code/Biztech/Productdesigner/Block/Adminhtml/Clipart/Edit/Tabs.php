<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Clipart\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_productdesigner_clipart_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Clipart Category Information'));
    }
}
