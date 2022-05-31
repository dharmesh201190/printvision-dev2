<?php

namespace LR\ArtworkDesign\Controller\Adminhtml\Items;

class NewAction extends \LR\ArtworkDesign\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
