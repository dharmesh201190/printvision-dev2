<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action
{
    protected $bannersliderModel;

    public function __construct(
        Context $context,
        \Biztech\Magemobcart\Model\Bannerslider $bannersliderModel
    ) {
        parent::__construct($context);
        $this->_bannersliderModel = $bannersliderModel;
    }
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_bannersliderModel;
        if (isset($id)) {
            $model->load($id)->delete();
            $this->messageManager->addSuccess(__('Successfully banner slider removed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
