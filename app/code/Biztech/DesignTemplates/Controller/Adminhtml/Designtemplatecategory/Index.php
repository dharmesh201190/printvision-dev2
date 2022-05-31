<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory;

class Index extends \Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory
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
        $resultPage->setActiveMenu('Biztech_DesignTemplates::designtemplatecategory');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Design Template Categories'));
        return $resultPage;
    }
}
