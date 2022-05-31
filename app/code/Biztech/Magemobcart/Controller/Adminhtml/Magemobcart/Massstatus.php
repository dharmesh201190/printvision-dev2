<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Magemobcart;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massstatus extends Action
{
    protected $magemobcartModel;
    protected $resultPageFactory;
    protected $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Biztech\Magemobcart\Model\Magemobcart $magemobcartModel,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_magemobcartModel = $magemobcartModel;
        $this->_registry = $registry;
    }
    public function execute()
    {
        $magemobcartIds = $this->getRequest()->getParam('magemobcart');
        if (!is_array($magemobcartIds)) {
            $this->messageManager->addError(__('Please select categories'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            foreach ($magemobcartIds as $magemobcartId) {
                $magemobcart = $this->_magemobcartModel->load($magemobcartId);
                $magemobcart->load($magemobcartId)->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
            }
            $this->messageManager->addSuccess(__('Successfully category block status changed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
