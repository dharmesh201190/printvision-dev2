<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Subtabs;

class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Subtabs {

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');
        $this->setAdminTitles($resultPage, $id);
        $subtabsCollection = $this->subtabsCollection->create();
        $subtabsModel = $this->subtabsFactory->create();
        if ($id) {
            $subtabsModel->load($id);
            if (!$subtabsModel->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/subtabs');
                return;
            }
        }
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $subtabsModel->addData($data);
        }
        $this->setDataInRegistry($subtabsModel, $subtabsCollection);
        $this->_initAction();
        $this->_view->renderLayout();
    }

    private function setAdminTitles($resultPage, $id) {
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Sub Tabs'));
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Sub Tab'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Add Sub Tab'));
        }
    }

    private function setDataInRegistry($subtabsModel, $subtabsCollection) {
        $this->_coreRegistry->register('biztech_productdesigner_subtabs', $subtabsModel);
        $this->_coreRegistry->register('biztech_productdesigner_subtabs_collection', $subtabsCollection);
    }

}
