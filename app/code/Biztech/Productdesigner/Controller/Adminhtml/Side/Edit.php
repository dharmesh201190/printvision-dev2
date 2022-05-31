<?php

namespace Biztech\Productdesigner\Controller\Adminhtml\Side;
class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Side
{

    public function execute()
    {
        $resultPage = $this->setResultPage();
        $id = $this->getRequest()->getParam('id');
        if($id)
        {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Image Side'))); 
        }
        $model = $this->sideFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/side');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_biztech_productdesigner_side', $model);
        $this->_initAction();
        $this->_view->renderLayout();
    }

    protected function setResultPage() {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Image Sides'));
        $resultPage->getConfig()->getTitle()->prepend((__('Add Image Side')));

        return $resultPage;
    }
}
