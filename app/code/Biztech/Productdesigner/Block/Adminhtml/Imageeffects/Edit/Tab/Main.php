<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Imageeffects\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {


  public function __construct(
   \Magento\Backend\Block\Template\Context $context,
   \Magento\Framework\Registry $registry,
   \Magento\Framework\Data\FormFactory $formFactory,
   array $data = []
 ) {
    parent::__construct($context,$registry,$formFactory,$data);
  }
    public function getTabLabel() {
      return __('Image Effects and Filters Information');
    }

    public function getTabTitle() {
      return __('Image Effects and Filters Information');
    }

    public function canShowTab() {
      return true;
    }

    public function isHidden() {
      return false;
    }

    protected function _prepareForm() {
      $model = $this->_coreRegistry->registry('current_biztech_productdesigner_imageeffects');
      $id = $this->getRequest()->getParam('id');
      /** @var \Magento\Framework\Data\Form $form */
      $form = $this->_formFactory->create();

      if($id)
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Image Effects and Filters')]);
      else
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add Image Effects and Filters')]);
      if ($model->getId()) {
        $fieldset->addField('effect_id', 'hidden', ['name' => 'id']);
      }
      

      $fieldset->addField(
           'effect_name', 'label', 
           [
               'name' => 'effect_name',
               'label' => __('Effect Name'),
               'title' => __('Effect Name'),
               'values' => $model->getEffectName()
           ]
       );

      if($model->getIsFilter()){
        $fieldset->addField('effect_image', 'image', array(
          'label'     => __('Effect Image'),
          'required'  => false,
          'name'      => 'effect_image',
          'note' => 'Accepted Files : jpg, jpeg, gif, png'
        ));
      }

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
