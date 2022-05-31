<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Fonts;
class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Fonts
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner:module');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Fonts'));
        return $resultPage;
    }
}
