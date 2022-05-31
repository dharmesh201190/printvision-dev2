<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Subtabs\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    protected function _construct() {
        parent::_construct();
        $this->setId('biztech_productdesigner_subtabs_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subtabs Information'));
    }

}
