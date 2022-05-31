<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;
class Edit extends \Biztech\Productdesigner\Controller\Adminhtml\Printablecolor
{

    public function execute()
    {
	 $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Printable Colors'));
        $id = $this->getRequest()->getParam('id');
	    if($id)
        {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Printable Color')));
        }
        else
        {
            $resultPage->getConfig()->getTitle()->prepend((__('Add Printable Color')));   
        }
       
        
        $model = $this->printableColorCollection->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('productdesigner/Printablecolor');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }

        
        $this->_coreRegistry->register('current_biztech_productdesigner_printablecolor', $model);
        
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
