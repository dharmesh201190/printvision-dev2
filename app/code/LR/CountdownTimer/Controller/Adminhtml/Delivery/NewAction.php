<?php

namespace LR\CountdownTimer\Controller\Adminhtml\Delivery;

class NewAction extends \LR\CountdownTimer\Controller\Adminhtml\Delivery
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
