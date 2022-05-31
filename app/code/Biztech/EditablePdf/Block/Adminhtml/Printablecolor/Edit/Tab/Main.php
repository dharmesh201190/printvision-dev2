<?php
namespace Biztech\EditablePdf\Block\Adminhtml\Printablecolor\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    public function getTabLabel()
    {
        return __('Printable Color Information');
    }
    public function getTabTitle()
    {
        return __('Printable Color Information');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_biztech_productdesigner_printablecolor');
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        if ($id)
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Printable Color')]);
        else
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Printable Color')]);
        if ($model->getId()) {
            $fieldset->addField('printablecolor_id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'color_name', 'text', ['name' => 'color_name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
       
        $eventElem = $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name' => 'stores[]',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $colorElement = $fieldset->addField(
                'color_code', 'text', ['name' => 'color_code','id' => 'color_code','label' => __('Code'), 'class' => __('color'), 'title' => __('Code'), 'required' => true, 'note' => 'Click to view Color Picker']
        );
        $colorElement->setAfterElementHtml('<input type="hidden" id="color_picker_path" value="' . $this->getViewFileUrl('Biztech_Productdesigner/js/jscolor/') . '" />');

        $fieldset->addField(
            'color_c', 'text', ['name' => 'color_c','id' => 'color_c','label' => __('C'), 'title' => __('C'),'required' => true,'class' => 'validate-number validate-digits-range digits-range-0-100','note' => 'Value should be between 0 to 100']
        );

        $fieldset->addField(
            'color_m', 'text', ['name' => 'color_m','id' => 'color_m', 'label' => __('M'), 'title' => __('M'),'required' => true,'class' => 'validate-number validate-digits-range digits-range-0-100','note' => 'Value should be between 0 to 100']
        );

        $fieldset->addField(
            'color_y', 'text', ['name' => 'color_y','id' => 'color_y','label' => __('Y'), 'title' => __('Y'),'required' => true,'class' => 'validate-number validate-digits-range digits-range-0-100','note' => 'Value should be between 0 to 100']
        );

        $fieldset->addField(
            'color_k', 'text', ['name' => 'color_k','id' => 'color_k', 'label' => __('K'), 'title' => __('K'),'required' => true,'class' => 'validate-number validate-digits-range digits-range-0-100','note' => 'Value should be between 0 to 100']
        );


       $eventElem->setAfterElementHtml("   
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate, confirmation) {
                    $('#color_code').change(function(){
                        var hex = $(this).val();
                        var computedC = 0;
                        var computedM = 0;
                        var computedY = 0;
                        var computedK = 0;

                        hex = (hex.charAt(0)=='#') ? hex.substring(1,7) : hex;

                        if (hex.length < 1) {
                          alert ('Please enter input hex value');   
                          return; 
                        }
                        if (hex.length != 6) {
                          alert ('Invalid length of the input hex value!');   
                          return; 
                        }
                        if (/[0-9a-f]{6}/i.test(hex) != true) {
                          alert ('Invalid digits in the input hex value!');
                          return; 
                        }

                        var r = parseInt(hex.substring(0,2),16); 
                        var g = parseInt(hex.substring(2,4),16); 
                        var b = parseInt(hex.substring(4,6),16); 

                        if (r==0 && g==0 && b==0) {
                          computedK = 1;
                          return [0,0,0,1];
                        }

                        computedC = 1 - (r/255);
                        computedM = 1 - (g/255);
                        computedY = 1 - (b/255);

                        var minCMY = Math.min(computedC,Math.min(computedM,computedY));

                        computedC = ((computedC - minCMY) / (1 - minCMY)) * 100 ;
                        computedM = ((computedM - minCMY) / (1 - minCMY)) * 100 ;
                        computedY = ((computedY - minCMY) / (1 - minCMY)) * 100;
                        computedK = minCMY * 100;
                        jQuery('#color_c').val(Math.floor(computedC));
                        jQuery('#color_m').val(Math.floor(computedM));
                        jQuery('#color_y').val(Math.floor(computedY));
                        jQuery('#color_k').val(Math.floor(computedK));
                        setTimeout(function(){
                            alert('Please verify all CMYK value');
                        },1000);
                    });
                }

            );
            </script>"
        );

        // $fieldset->addField(
        //     'pantone_color', 'text', ['name' => 'pantone_color', 'label' => __('Pantone Color'),'title' => __('Pantone Color'), 'required' => true, 'note' => 'Click to view Color Picker']
        // );

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
