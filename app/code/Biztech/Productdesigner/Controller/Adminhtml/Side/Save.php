<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Side;
class Save extends \Biztech\Productdesigner\Controller\Adminhtml\Side
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $imageSideModel = $this->sideFactory->create();
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');      
                
                $this->setImageSideColException($data, $id, $imageSideModel);

                $this->setImageSideAndSessionData($data, $id, $imageSideModel);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('productdesigner/side/edit', ['id' => $imageSideModel->getId()]);
                    return;
                }
                $this->_redirect('productdesigner/side/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->setLocalizedExceptionCatch($e, $id);
                return;
            } catch (\Exception $e) {
                $this->setOtherExceptionCatch($e, $data);
                return;
            }
        }
        $this->_redirect('productdesigner/side/');
    }

    protected function setImageSideColException($data, $id, $imageSideModel) {
        $imageSidecol = $this->sideCollection->create()->addFieldToFilter('imageside_title', array('eq' => strtolower($data['imageside_title'])));
        if (!$id) {
            if(count($imageSidecol)){
                throw new \Magento\Framework\Exception\LocalizedException(__('Duplicate Image Side'));
            }
        }          
        if ($id) {
            $imageSideModel->load($id);
            if ($id != $imageSideModel->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
            }
        }
    }

    protected function setImageSideAndSessionData($data, $id, $imageSideModel) {
        $imageSideModel->setData($data)->setId($id);
        $this->session->setPageData($imageSideModel->getData());
        $imageSideModel->save();
        $this->messageManager->addSuccess(__('Side was successfully saved.'));
        $this->session->setPageData(false);
    }

    protected function setLocalizedExceptionCatch($e, $id) {
        $this->messageManager->addError($e->getMessage());
        $id = (int)$this->getRequest()->getParam('id');
        if (!empty($id)) {
            $this->_redirect('productdesigner/side/edit', ['id' => $id]);
        } else {
            $this->_redirect('productdesigner/side/new');
        }
    }

    protected function setOtherExceptionCatch($e, $data) {
        $this->messageManager->addError(
            __('Something went wrong while saving the item data. Please review the error log.')
        );
        $this->logger->critical($e);
        $this->session->setPageData($data);
        $this->_redirect('productdesigner/side/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
