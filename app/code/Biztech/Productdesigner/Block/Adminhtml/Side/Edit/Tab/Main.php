<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Side\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {

    public function getTabLabel() {
        return __('Image Side Information');
    }

    public function getTabTitle() {
        return __('Side Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    protected function _prepareForm() {

        $model = $this->_coreRegistry->registry('current_biztech_productdesigner_side');
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        if($id)
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Image Side')]);
        else
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Image Side')]);
        if ($model->getId()) {
            $fieldset->addField('imageside_id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
                'imageside_title', 'text', ['name' => 'imageside_title', 'label' => __('Image Side'), 'title' => __('Image Side'), 'required' => true]
        );

        $fieldset->addField(
                'status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('status'), 'values' => array(
                array(
                    'value' => 1,
                    'label' => __('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => __('Disabled')
                ))
                ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
