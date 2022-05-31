<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;
class Delete extends \Biztech\Productdesigner\Controller\Adminhtml\Printablecolor
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->printableColorCollection->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Printable Color was deleted successfully.'));
                $this->_redirect('productdesigner/printablecolor/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('productdesigner/printablecolor/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('productdesigner/printablecolor/');
    }
}
