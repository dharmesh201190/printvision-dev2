<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Clipart\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {

    protected $_clipartCollection;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Biztech\Productdesigner\Model\Mysql4\Clipart\Collection $clipartCollection,
        \Magento\Store\Model\System\Store $systemStore, array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
        $this->_clipartCollection = $clipartCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel() {
        return __('Clipart Category Information');
    }

    public function getTabTitle() {
        return __('Clipart Category Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
    }

    protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('current_biztech_productdesigner_clipart');
        $model1 = $this->_coreRegistry->registry('current_biztech_productdesigner_clipart1');
        $collection = ($model1->getData());
        $id = $this->getRequest()->getParam('id');

        $template_array = [['label' => __('Please Select Category'), 'value' => '']];

        foreach ($collection as $clipartcategry) {
            $level = $clipartcategry['level'];
            if ($id != $clipartcategry['clipart_id'] && $level <= 1) {
                $label = $clipartcategry['clipart_title'];
                $padLength = $level + strlen($label);
                $label_new = str_pad($label, $padLength, "-", STR_PAD_LEFT);
                $template_array[] = array(
                    'label' => $label_new,
                    'value' => $clipartcategry['clipart_id']
                );
            }
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();


        if (isset($id)) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Clipart Category')]);
        } else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Clipart Category')]);
        }
        if ($model->getId()) {
            $fieldset->addField('clipart_id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'clipart_title', 'text', ['name' => 'clipart_title', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );
        $fieldset->addField(
            'is_root_category', 'checkbox', ['name' => 'is_root_category', 'label' => __('Assign as Root Category?'), 'title' => __('Assign as Root Category?'), 'onchange' => "showParentCategories(this)", 'onclick' => "this.value = this.checked ? 1 : 0;"]
        );
        $form->getElement('is_root_category')->setIsChecked(!empty($model['is_root_category']));
        $eventElem = $fieldset->addField(
            'parent_categories', 'select', ['name' => 'parent_categories', 'label' => __('Category'), 'title' => __('Category'), 'disabled' => false, 'values' => $template_array, 'required' => true]
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

                //jQuery("parent_categories").disabled=false;
                    jQuery("#parent_categories").prop("disabled",true);
                }
                else{

                //jQuery("parent_categories").disabled=true;
                    jQuery("#parent_categories").prop("disabled",false);
                }
            }

            </script>');
        $form->setValues($model->getData());
        /* media uploader */
        $field = $fieldset->addField(
            'customfield', 'text', [
                'name' => 'customfield',
                'title' => __('Custom Field'),
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Biztech\Productdesigner\Block\Adminhtml\Clipart\Gallery\Content');
        $field->setRenderer($renderer);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
