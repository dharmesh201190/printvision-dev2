<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Magemobcart;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massdelete extends Action
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
            $this->messageManager->addError(__('Please select category block'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            foreach ($magemobcartIds as $magemobcartId) {
                $magemobcartlider = $this->_magemobcartModel->load($magemobcartId);
                $magemobcartlider->delete();
            }
            $this->messageManager->addSuccess(__('Successfully category block removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
