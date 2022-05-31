<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Side;
class NewAction extends \Biztech\Productdesigner\Controller\Adminhtml\Side
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
