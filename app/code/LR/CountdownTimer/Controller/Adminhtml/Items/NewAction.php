<?php

namespace LR\CountdownTimer\Controller\Adminhtml\Items;

class NewAction extends \LR\CountdownTimer\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
