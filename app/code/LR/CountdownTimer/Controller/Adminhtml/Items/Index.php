<?php

namespace LR\CountdownTimer\Controller\Adminhtml\Items;

class Index extends \LR\CountdownTimer\Controller\Adminhtml\Items
{
    /**
     * Holidays list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('LR_CountdownTimer::test');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Holidays'));
        $resultPage->addBreadcrumb(__('Manage'), __('Holidays'));
        $resultPage->addBreadcrumb(__('Manage'), __('Holidays'));
        return $resultPage;
    }
}