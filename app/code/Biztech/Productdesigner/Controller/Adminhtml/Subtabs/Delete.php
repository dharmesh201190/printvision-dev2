<?php


namespace Biztech\Productdesigner\Controller\Adminhtml\Subtabs;

class Delete extends \Biztech\Productdesigner\Controller\Adminhtml\Subtabs {

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->deleteById($id);
                $this->messageManager->addSuccess(__('You deleted the item.'));
                $this->_redirect('productdesigner/subtabs/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('productdesigner/subtabs/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('productdesigner/subtabs/');
    }

    private function deleteById($id) {
        $subtabModel = $this->subtabsFactory->create();
        $subtabModel->load($id);
        $subtabModel->delete();
    }

}
