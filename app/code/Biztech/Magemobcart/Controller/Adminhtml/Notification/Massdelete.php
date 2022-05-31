<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Notification;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massdelete extends Action
{
    protected $bannersliderModel;
    protected $resultPageFactory;
    protected $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Biztech\Magemobcart\Model\Notification $notificationModel,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_notificationModel = $notificationModel;
        $this->_registry = $registry;
    }
    public function execute()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            $this->messageManager->addError(__('Please select notification'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            foreach ($notificationIds as $notificationId) {
                $notification = $this->_notificationModel->load($notificationId);
                $notification->delete();
            }
            $this->messageManager->addSuccess(__('Successfully notification removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
