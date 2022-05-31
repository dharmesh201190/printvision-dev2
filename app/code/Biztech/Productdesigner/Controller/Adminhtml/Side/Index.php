<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Side;
class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Side
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner:side');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Image Sides'));
        
        return $resultPage;
    }
}
