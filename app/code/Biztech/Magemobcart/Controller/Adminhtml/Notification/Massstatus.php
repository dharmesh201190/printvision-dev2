<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massstatus extends Action
{
    protected $bannersliderModel;
    protected $resultPageFactory;
    protected $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Biztech\Magemobcart\Model\Bannerslider $bannersliderModel,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_bannersliderModel = $bannersliderModel;
        $this->_registry = $registry;
    }
    public function execute()
    {
        $bannersliderIds = $this->getRequest()->getParam('bannerslider');
        if (!is_array($bannersliderIds)) {
            $this->messageManager->addError(__('Please select banners'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            foreach ($bannersliderIds as $bannersliderId) {
                $bannerslider = $this->_bannersliderModel->load($bannersliderId);
                $bannerslider->load($bannersliderId)->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
            }
            $this->messageManager->addSuccess(__('Successfully banner slider removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
