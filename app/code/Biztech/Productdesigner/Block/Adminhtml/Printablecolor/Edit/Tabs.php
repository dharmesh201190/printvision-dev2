<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Printablecolor\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_productdesigner_printablecolor_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Printable Color Information'));
    }
}
