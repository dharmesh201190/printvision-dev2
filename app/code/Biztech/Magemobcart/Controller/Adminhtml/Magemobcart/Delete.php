<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Magemobcart;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->_magemobcartModel;
        if ($id) {
            $model->load($id)->delete();
            $this->messageManager->addSuccess(__('Successfully category block removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
