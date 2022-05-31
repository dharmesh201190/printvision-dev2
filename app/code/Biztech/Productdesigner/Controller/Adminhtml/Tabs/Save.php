<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Tabs;

class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Tabs {

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            try {
                $tabModel = $this->saveTabs();
                $this->messageManager->addSuccess(__('Tabs were successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/tabs/edit', ['id' => $tabModel->getId()]);
                    return;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                if (!empty($id)) {
                    $this->_redirect('productdesigner/tabs/edit', ['id' => $id]);
                } else {
                    $this->_redirect('productdesigner/tabs/new');
                }
                return;
            }
        }
        $this->_redirect('productdesigner/tabs/');
    }

    private function saveTabs() {
        $tabModel = $this->tabsFactory->create();
        $inputFilter = new \Zend_Filter_Input(
                [], [], $this->getRequest()->getPostValue()
        );
        $data = $inputFilter->getUnescaped();
        $id = $this->getRequest()->getParam('id');
        
        if ($id) {
            $tabModel->load($id);
            if ($id != $tabModel->getId()) {
                $this->messageManager->addError(__('The wrong item is specified.'));
            }
        }
        
        $tabModel->setData($data)->setId($id);
        $tabModel->save();

        return $tabModel;
    }

}
