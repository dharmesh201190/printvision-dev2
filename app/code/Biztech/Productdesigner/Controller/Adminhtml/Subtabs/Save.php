<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Subtabs;

class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Subtabs {

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            try {
                $subtabModel = $this->saveSubtabs();
                $this->messageManager->addSuccess(__('Subtabs were successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/subtabs/edit', ['id' => $subtabModel->getId()]);
                    return;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                if (!empty($id)) {
                    $this->_redirect('productdesigner/subtabs/edit', ['id' => $id]);
                } else {
                    $this->_redirect('productdesigner/subtabs/new');
                }
                return;
            }
        }
        $this->_redirect('productdesigner/subtabs/');
    }

    private function saveSubtabs() {
        $subtabModel = $this->subtabsFactory->create();
        $inputFilter = new \Zend_Filter_Input(
                [], [], $this->getRequest()->getPostValue()
        );
        $data = $inputFilter->getUnescaped();
        $subtabs = implode(",", $data['subtabs']);
        $data['subtabs'] = $subtabs;
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $subtabModel->load($id);
            if ($id != $subtabModel->getId()) {
                $this->messageManager->addError(__('The wrong item is specified.'));
            }
        }
        $subtabModel->setData($data)->setId($id);
        $subtabModel->save();
        return $subtabModel;
    }

}
