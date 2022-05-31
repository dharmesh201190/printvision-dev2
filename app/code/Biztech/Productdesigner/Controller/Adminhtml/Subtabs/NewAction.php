<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Subtabs;

class NewAction extends \Biztech\Productdesigner\Controller\Adminhtml\Subtabs {

    public function execute() {
        $this->_forward('edit');
    }

}
