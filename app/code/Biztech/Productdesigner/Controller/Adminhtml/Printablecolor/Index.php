<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;
class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Printablecolor
{

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner:printablecolor');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Printable Colors'));
        
       
        return $resultPage;
    }
}
