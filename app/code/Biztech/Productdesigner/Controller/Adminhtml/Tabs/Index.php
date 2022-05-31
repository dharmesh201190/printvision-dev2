<?php


namespace Biztech\Productdesigner\Controller\Adminhtml\Tabs;

class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Tabs {

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner:tabs');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Tabs'));
        return $resultPage;
    }

}
