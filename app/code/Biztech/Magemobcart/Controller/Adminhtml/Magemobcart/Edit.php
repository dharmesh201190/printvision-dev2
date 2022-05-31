<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Magemobcart;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Edit Banner action.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Edit extends Action
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
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_magemobcartModel;
        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This category block has no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject = $this->_registry;
        $registryObject->register('magemobcart_magemobcart_data', $model);
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Biztech_Magemobcart::magemobcart');
        $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? __('Edit Featured Category') : __('Add Featured Category'));
        return $resultPage;
    }
}
