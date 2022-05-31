<?php

namespace LR\ArtworkDesign\Controller\Adminhtml\Items;

class Index extends \LR\ArtworkDesign\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('LR_ArtworkDesign::test');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Artwork Design'));
        $resultPage->addBreadcrumb(__('Manage'), __('Manage'));
        $resultPage->addBreadcrumb(__('Artwork Design'), __('Artwork Design'));
        return $resultPage;
    }
}