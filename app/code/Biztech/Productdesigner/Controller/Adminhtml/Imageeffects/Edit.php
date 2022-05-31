<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Imageeffects;

class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Imageeffects
{
   

    public function execute()
    {
	 $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Image Effects and Filters'));
        $id = $this->getRequest()->getParam('id');
        if($id)
        {
	       $resultPage->getConfig()->getTitle()->prepend((__('Edit Image Effects and Filters')));
        }
        else
        {
           $resultPage->getConfig()->getTitle()->prepend((__('Add Image Effects and Filters')));   
        }
        $model = $this->imageEffectsFactory->create();
        
        if ($id) {
            $model->load($id);            
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/imageeffects');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_biztech_productdesigner_imageeffects', $model);
        
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
