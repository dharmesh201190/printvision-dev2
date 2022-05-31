<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory;

class Save extends \Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory {

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_designtemplatecategory;
                $data = $this->getRequest()->getPostValue();
                $templates = array();


                /* Prevent to add duplicate category */
                if(!empty($data['category_title']) && empty($data['id'])){
                    $checkDuplicateCat = $model->getCollection()->addFieldToFilter('category_title' , $data['category_title'])->getFirstItem()->getData();
                    $parentCategory = !empty($data['parent_categories']) ? $data['parent_categories'] : 0;
                    $currentParentCategory = !empty($checkDuplicateCat['parent_categories']) ? $checkDuplicateCat['parent_categories'] : 0;
                    if(sizeof($checkDuplicateCat) > 0 && ($currentParentCategory == $parentCategory)){
                        $this->messageManager->addError(__('Category already exist.'));
                        $this->_redirect('designtemplates/designtemplatecategory/new');
                        return;
                    }   
                }

                if(!empty($data['category_title']) && !empty($data['id'])){
                    $checkDuplicateCat = $model->getCollection()->addFieldToFilter('category_title' , $data['category_title'])->addFieldToFilter('id',['neq' => $data['id']])->getFirstItem()->getData();
                    $parentCategory = !empty($data['parent_categories']) ? $data['parent_categories'] : 0;
                    $currentParentCategory = !empty($checkDuplicateCat['parent_categories']) ? $checkDuplicateCat['parent_categories'] : 0;
                    if(sizeof($checkDuplicateCat) > 0 && ($currentParentCategory == $parentCategory)){
                        $this->messageManager->addError(__('Category already exist.'));
                        $this->_redirect('designtemplates/designtemplatecategory/edit', ['id' => $data['id']]);
                        return;
                    }   
                }

                if (isset($data['links'])) {
                    $templates = $this->_backend_helper->decodeGridSerializedInput($data['links']['templates']);
                }
                $keys = array();
                foreach ($templates as $key => $template):
                    $keys[] = $key;
                endforeach;

                $data['designs'] = implode(',', $keys);

                $inputFilter = new \Zend_Filter_Input(
                        [], [], $data
                );

                $data = $inputFilter->getUnescaped();

                if (isset($data['parent_categories'])) {
                    $data['is_root_category'] = 0;
                    $parent_category = $data['parent_categories'];
                    $parent_category_level = $this->_designtemplatecategory->load($parent_category)->getLevel();
                    $data['level'] = $parent_category_level + 1;
                }
                if ($data['is_root_category'] == 1) {
                    $data['parent_categories'] = '';
                    $data['level'] = 0;
                }

                $id = $this->getRequest()->getParam('id');

                if (isset($data['stores'])) {
                    if (in_array('0', $data['stores'])) {
                        $data['store_id'] = '0';
                    } else {
                        $data['store_id'] = implode(",", $data['stores']);
                    }
                    unset($data['stores']);
                }
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }

                $model->setData($data)->setId($id);
                $this->_session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('Category was successfully saved.'));
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('designtemplates/designtemplatecategory/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('designtemplates/designtemplatecategory/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int) $this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('designtemplates/designtemplatecategory/edit', ['id' => $id]);
                } else {
                    $this->_redirect('designtemplates/designtemplatecategory/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                        __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_redirect('designtemplates/designtemplatecategory/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('designtemplates/designtemplatecategory/');
    }

}
