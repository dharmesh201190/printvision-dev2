<?php

namespace Biztech\Productdesigner\Block\Adminhtml\Subtabs\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {

    protected $allMainTabs;
    protected $allSubTabs;
    protected $objectManager;
    protected $scopeConfig;

    public function getTabLabel() {
        return __('Subtabs Information');
    }

    public function getTabTitle() {
        return __('Subtabs Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    protected function _prepareForm() {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subtabsModel = $this->_coreRegistry->registry('biztech_productdesigner_subtabs');
        $subtabsCollection = $this->_coreRegistry->registry('biztech_productdesigner_subtabs_collection')->getData();
        $id = $this->getRequest()->getParam('id');
        $main_tabs_exist = array();
        foreach ($subtabsCollection as $subtabs_data) {
            if ($id && $id != $subtabs_data['subtabs_id']) {
                $main_tabs_exist[] = $subtabs_data['maintab'];
            } else if ($id != $subtabs_data['subtabs_id']) {
                $main_tabs_exist[] = $subtabs_data['maintab'];
            }
        }
        $selectedMainTabs = $this->setMainTabs($main_tabs_exist);
        $main_tabs_check = $this->getMainTabs($id, $subtabsCollection);
        $this->setSubTabs($main_tabs_check, $selectedMainTabs);
        $this->createForm($id, $subtabsModel);
        return parent::_prepareForm();
    }
    private function setMainTabs($main_tabs_exist) {
        $this->allMainTabs = $this->objectManager->create('Biztech\Productdesigner\Model\Adminhtml\Config\Source\Maintab')->toOptionArray();
        $selected_main_tabs = $this->objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('productdesigner/layout_general/main_tab');
        $selectedMainTabs = explode(',', $selected_main_tabs);
        foreach ($this->allMainTabs as $key => $value) {
            if (!in_array($value['value'], $selectedMainTabs) || in_array($value['value'], $main_tabs_exist)) {
                unset($this->allMainTabs[$key]);
            }
        }
        return $selectedMainTabs;
    }
    private function setSubTabs($main_tabs_check, $selectedMainTabs) {
        $this->allSubTabs = $this->objectManager->create('Biztech\Productdesigner\Model\Adminhtml\Config\Source\Maintab')->toOptionArray();
        foreach ($this->allSubTabs as $key => $value) {
            if (in_array($value['value'], $selectedMainTabs) || in_array($value['value'], $main_tabs_check)) {
                unset($this->allSubTabs[$key]);
            }
        }
    }
    private function getMainTabs($id, $subtabsCollection) {
        $main_tabs_exist = array();
        foreach ($subtabsCollection as $subtabs_data) {
            if ($id && $id != $subtabs_data['subtabs_id']) {
                $subtabs_data_array = explode(",", $subtabs_data['subtabs']);
                foreach ($subtabs_data_array as $subtab) {
                    $main_tabs_exist[] = $subtab;
                }
            } else if ($id != $subtabs_data['subtabs_id']) {
                $subtabs_data_array = explode(",", $subtabs_data['subtabs']);
                foreach ($subtabs_data_array as $subtab) {
                    $main_tabs_exist[] = $subtab;
                }
            }
        }
        return $main_tabs_exist;
    }
    private function createForm($id, $subtabsModel) {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $this->addFieldsetInForm($form, $id, $subtabsModel->getId());
        $form->setValues($subtabsModel->getData());
        $this->setForm($form);
    }
    private function addFieldsetInForm($form, $id, $subtabId) {
        if ($id) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Sub Tab')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Sub Tab')]);
        }
        if ($subtabId) {
            $fieldset->addField('subtabs_id', 'hidden', ['name' => 'id']);
        }
        $this->addFieldInFieldset($fieldset);
    }
    private function addFieldInFieldset($fieldset) {
        $fieldset->addField(
            'maintab', 'select', [
            'name' => 'maintab',
            'label' => __('Main Tab'),
            'title' => __('Main Tab'),
            'values' => $this->allMainTabs,
            'required' => true,
            'note' => __('Choose a Tab from all Main Tab options'),
                ]
        );
        $fieldset->addField(
            'subtabs', 'multiselect', [
            'name' => 'subtabs',
            'label' => __('Sub Tabs'),
            'title' => __('Sub Tabs'),
            'values' => $this->allSubTabs,
            'required' => true,
            'note' => __('Choose a Tab from all Main Tab options'),
                ]
        );
    }
}
