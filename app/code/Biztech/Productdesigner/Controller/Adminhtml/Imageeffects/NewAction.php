<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Imageeffects;

class NewAction extends \Biztech\Productdesigner\Controller\Adminhtml\Imageeffects
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
