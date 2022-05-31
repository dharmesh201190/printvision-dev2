<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;
class NewAction extends \Biztech\Productdesigner\Controller\Adminhtml\Printablecolor
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
