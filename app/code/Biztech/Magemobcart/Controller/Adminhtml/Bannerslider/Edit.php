<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
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
        $id = $this->getRequest()->getParam('id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_bannersliderModel;
        if (isset($id)) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This banner no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject = $this->_registry;
        $registryObject->register('magemobcart_bannerslider_data', $model);
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Magemobcart::bannerslider');
        $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? __('Edit Banner Slider') : __('Add Banner Slider'));
        return $resultPage;
    }
}
