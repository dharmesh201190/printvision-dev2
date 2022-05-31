<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Tabs;

class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Tabs {

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');
        $this->setAdminTitles($resultPage, $id);
        $tabsCollection = $this->tabsCollection->create();
        $tabsModel = $this->tabsFactory->create();
        if ($id) {
            $tabsModel->load($id);
            if (!$tabsModel->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/tabs');
                return;
            }
        }
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $tabsModel->addData($data);
        }

        
        $this->setDataInRegistry($tabsModel, $tabsCollection);
        $this->_initAction();
        $this->_view->renderLayout();
    }

    private function setAdminTitles($resultPage, $id) {
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Tabs'));
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Tab'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Insert Tab'));
        }
    }

  
    private function setDataInRegistry($tabsModel, $tabsCollection) {
        $this->_coreRegistry->register('biztech_productdesigner_tabs', $tabsModel);
        $this->_coreRegistry->register('biztech_productdesigner_tabs_collection', $tabsCollection);
    }

}
