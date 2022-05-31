<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Side;
class Delete extends \Biztech\Productdesigner\Controller\Adminhtml\Side
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->sideFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Side was successfully deleted.'));
                $this->_redirect('productdesigner/side/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->setCatch(); 
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('productdesigner/side/');
    }

    protected function setCatch() {
        $this->messageManager->addError(
            __('We can\'t delete item right now. Please review the log and try again.')
        );
        $this->logger->critical($e);
        $this->_redirect('productdesigner/side/edit', ['id' => $this->getRequest()->getParam('id')]);
        return;
    }
}
