<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Printablecolor\Edit\Tab;

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
                'color_code', 'text', ['name' => 'color_code', 'label' => __('Code'), 'class' => __('color'), 'title' => __('Code'), 'required' => true, 'note' => 'Click to view Color Picker']
        );
        $colorElement->setAfterElementHtml('<input type="hidden" id="color_picker_path" value="' . $this->getViewFileUrl('Biztech_Productdesigner/js/jscolor/') . '" />');

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
