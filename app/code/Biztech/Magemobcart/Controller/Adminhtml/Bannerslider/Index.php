<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;
    protected $resultPage;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->resultPage = $this->_resultPageFactory->create();
        $this->resultPage->setActiveMenu('Biztech_Magemobcart::bannerslider');
        $this->resultPage->getConfig()->getTitle()->prepend((__('Manage Banner Slider')));
        return $this->resultPage;
    }
}
