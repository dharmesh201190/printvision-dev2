<?php

namespace Biztech\Productdesigner\Block\Adminhtml\Tabs\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {

    protected $allMainTabs;
    protected $allCustomeLabel;
    protected $objectManager;

    public function getTabLabel() {
        return __('Tabs Information');
    }

    public function getTabTitle() {
        return __('Tabs Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    protected function _prepareForm() {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $tabsModel = $this->_coreRegistry->registry('biztech_productdesigner_tabs');
        $tabsCollection = $this->_coreRegistry->registry('biztech_productdesigner_tabs_collection')->getData();

        $id = $this->getRequest()->getParam('id');
        $main_tabs_exist = array();
        foreach ($tabsCollection as $tabs_data) {
            $main_tabs_exist[] = $tabs_data['label'];
        }
        $selectedMainTabs = $this->setMainTabs($main_tabs_exist);
        $this->createForm($id, $tabsModel);
        return parent::_prepareForm();
    }
    private function setMainTabs($main_tabs_exist) {
        $this->allMainTabs = $this->objectManager->create('Biztech\Productdesigner\Model\Adminhtml\Config\Source\Maintab')->toOptionArray();
    }

    private function createForm($id, $tabsModel) {
        $form = $this->_formFactory->create();
        $this->addFieldsetInForm($form, $id, $tabsModel->getId());
        $form->setValues($tabsModel->getData());
        $this->setForm($form);
    }
    private function addFieldsetInForm($form, $id, $tabId) {
        if ($id) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Tab')]);
            $userGuidefieldset  = $form->addFieldset('userGuidefieldset', ['legend' => __('User Guide')]);
            $userGuidefieldset->setAfterElementHtml('
        
                <span>Enter Name</span>
                ');
            } else {
                $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Tab')]);
            }
            if ($tabId) {
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            }
            $this->addFieldInFieldset($fieldset,$userGuidefieldset);
        }
    private function addFieldInFieldset($fieldset,$userGuidefieldset) {
        $fieldset->addField(
            'label', 'label', 
            [
                'name' => 'label',
                'label' => __('Tab'),
                'title' => __('Tab'),
                'values' => $this->allMainTabs
            ]
        );
        $fieldset->addField(
            'custom_label', 'text', 
            [
                'name' => 'custom_label',
                'label' => __('Custom Label'),
                'title' => __('Custom Label'),
                'values' => $this->allMainTabs,
                'required' => true
            ]
        );
        $userGuidefieldset->addField(
            'first_tooltip', 'text', 
            [
                'name' => 'first_tooltip',
                'label' => __('Left Panel Tooltip'),
                'title' => __('Left Panel Tooltip'),
                'values' => $this->allMainTabs,
                'required' => false
            ]
        );
        $userGuidefieldset->addField(
            'second_tooltip', 'text', 
            [
                'name' => 'second_tooltip',
                'label' => __('Right Panel Tooltip'),
                'title' => __('Right Panel Tooltip'),
                'values' => $this->allMainTabs,
                'required' => false
            ]
        );
        
    }
}
