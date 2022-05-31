<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Side\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_productdesigner_side_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Image Side Information'));
    }
}
