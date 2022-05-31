<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory;

class Edit extends \Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory
{

    public function execute()
    {
	   $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('Product Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Design Template Categories'));
        $id = $this->getRequest()->getParam('id');
        if($id)
        {
	       $resultPage->getConfig()->getTitle()->prepend((__('Edit Design Template Category'))); 
        }
        else
        {
            $resultPage->getConfig()->getTitle()->prepend((__('Add Design Template Category')));    
        }
        
        $model = $this->_designtemplatecategory;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {                
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('DesignTemplates/designtemplatecategory');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_biztech_DesignTemplates_designtemplatecategory', $model);
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
