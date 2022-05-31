<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory;

class Delete extends \Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_designtemplatecategory->load($id)->delete();
                $this->messageManager->addSuccess(__('You deleted the item.'));
                $this->_redirect('designtemplates/designtemplatecategory/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_redirect('designtemplates/designtemplatecategory/edit', ['id' => $id]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('designtemplates/designtemplatecategory/');
    }
}
