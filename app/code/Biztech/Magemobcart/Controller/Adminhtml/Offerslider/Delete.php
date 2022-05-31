<?php

namespace Biztech\Magemobcart\Controller\Adminhtml\Offerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->_offersliderModel;
        if ($id) {
            $model->load($id)->delete();
            $this->messageManager->addSuccess(__('Successfully offer slider removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
