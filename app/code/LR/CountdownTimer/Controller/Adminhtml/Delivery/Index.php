<?php

namespace LR\CountdownTimer\Controller\Adminhtml\Delivery;

class Index extends \LR\CountdownTimer\Controller\Adminhtml\Delivery
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
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Delivery'));
        $resultPage->addBreadcrumb(__('Manage'), __('Delivery'));
        $resultPage->addBreadcrumb(__('Manage'), __('Delivery'));
        return $resultPage;
    }
}