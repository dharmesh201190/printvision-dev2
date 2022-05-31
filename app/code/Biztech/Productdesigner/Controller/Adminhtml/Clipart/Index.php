<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Clipart
{

    public function execute()
    {       
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner::clipart');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Clipart Categories'));
       
        return $resultPage;
    }
}
