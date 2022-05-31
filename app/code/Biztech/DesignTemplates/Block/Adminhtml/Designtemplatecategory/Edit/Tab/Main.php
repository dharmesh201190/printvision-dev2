<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Biztech\DesignTemplates\Block\Adminhtml\Designtemplatecategory\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Design Templates Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Category Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('current_biztech_DesignTemplates_designtemplatecategory');
        $model1 = $model->getCollection();

        $id = $this->getRequest()->getParam('id');

        $template_array = [['label' => __('Please Select Category'), 'value' => '']];
        $collection = ($model1->getData());
        foreach ($collection as $designtemplatescategry) {

            if ($id != $designtemplatescategry['id']) {
                if ($designtemplatescategry['level'] < 2) {
                    $seperator = '';
                    for ($i = 0; $i <= $designtemplatescategry['level']; $i++) {
                        $seperator .= '-';
                    }
                    $label = $designtemplatescategry['category_title'];

                    $template_array[] = array(
                        'label' => $seperator . $label,
                        'value' => $designtemplatescategry['id']
                    );
                }
            }
        }



        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        if (isset($id))
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Design Template Category')]);
        else
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Design Template Category')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
                'category_title', 'text', ['name' => 'category_title', 'label' => __('Category Title'), 'title' => __('Category Title'), 'required' => true]
        );

        $fieldset->addField(
                'is_root_category', 'checkbox', ['name' => 'is_root_category', 'label' => __('Is Root Category'), 'title' => __('Is Root Category'), 'onchange' => "showParentCategories(this)", 'checked' => false, 'onclick' => "this.value = this.checked ? 1 : 0;"]
        );

        $form->getElement('is_root_category')->setIsChecked(!empty($model['is_root_category']));

        $eventElem = $fieldset->addField(
                'parent_categories', 'select', ['name' => 'parent_categories', 'label' => __('Category'), 'title' => __('Category'), 'disabled' => false, 'values' => $template_array, 'required' => true,'note' => 'Allowed upto three level hierachy only']
        );

        $eventElem = $fieldset->addField(
                'store_id', 'multiselect', [
            'name' => 'stores[]',
            'label' => __('Store Views'),
            'title' => __('Store Views'),
            'required' => true,
            'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                ]
        );

        if(empty($model->getStatus())){
            $model->setStatus(1);
        }
        $fieldset->addField(
                'status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('status'),'value' => 1, 'values' => array(
                array(
                    'value' => 1,
                    'label' => __('Enabled')
                ),
                array(
                    'value' => 0,
                    'label' => __('Disabled')
                ))
                ]
        );

        /*
         * *For enable disable parent category based on is root category selection
         */
        $eventElem->setAfterElementHtml('<script>
            require(["jquery", "jquery/ui"], function ($) {
                jQuery(document).ready(function(){                                                   
                    if(jQuery("#is_root_category").val()==1){
                       jQuery("#parent_categories").prop("disabled",true);
                   }
                   else{
                    jQuery("#parent_categories").prop("disabled",false);
                }
                }); 
                });
                function showParentCategories(checkboxElem){            
                    if(checkboxElem.checked){
                        jQuery("#parent_categories").prop("disabled",true);
                    }
                    else{
                        jQuery("#parent_categories").prop("disabled",false);
                    }
                }

        </script>');
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
