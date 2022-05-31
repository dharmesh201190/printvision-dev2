<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Subtabs;

class Index extends \Biztech\Productdesigner\Controller\Adminhtml\Subtabs {

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Productdesigner:subtabs');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Sub Tabs'));
        return $resultPage;
    }

}
