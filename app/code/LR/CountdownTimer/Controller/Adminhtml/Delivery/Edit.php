<?php

namespace LR\CountdownTimer\Controller\Adminhtml\Delivery;

class Edit extends \LR\CountdownTimer\Controller\Adminhtml\Delivery
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('LR\CountdownTimer\Model\Delivery');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('lr_countdowntimer/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_lr_countdowntimer_delivery', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('delivery_delivery_edit');
        $this->_view->renderLayout();
    }
}
