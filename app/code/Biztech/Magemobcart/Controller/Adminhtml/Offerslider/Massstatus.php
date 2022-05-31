<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Offerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massstatus extends Action
{
    protected $offersliderModel;
    protected $resultPageFactory;
    protected $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Biztech\Magemobcart\Model\Offerslider $offersliderModel,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_offersliderModel = $offersliderModel;
        $this->_registry = $registry;
    }
    public function execute()
    {
        $offersliderIds = $this->getRequest()->getParam('offerslider');
        if (!is_array($offersliderIds)) {
            $this->messageManager->addError(__('Please select offers'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            foreach ($offersliderIds as $offersliderId) {
                $offerslider = $this->_offersliderModel->load($offersliderId);
                $offerslider->load($offersliderId)->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
            }
            $this->messageManager->addSuccess(__('Successfully offer slider status changed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
