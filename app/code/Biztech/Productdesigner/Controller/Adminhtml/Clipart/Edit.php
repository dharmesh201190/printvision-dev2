<?php


namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Clipart {

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        $model1 = $this->_clipartCollection;
        $this->prepareResultPage($id);
        $model = $this->clipartFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/clipart');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_biztech_productdesigner_clipart', $model);
        $this->_coreRegistry->register('current_biztech_productdesigner_clipart1', $model1);
        $this->_initAction();
        $this->_view->renderLayout();
    }

    protected function prepareResultPage($id) {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Clipart Category'));
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Clipart Category')));
        } else {
            $resultPage->getConfig()->getTitle()->prepend((__('Add Clipart Category')));
        }
    }

}
