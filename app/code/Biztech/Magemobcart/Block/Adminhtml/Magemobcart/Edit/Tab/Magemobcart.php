<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Magemobcart\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class Magemobcart extends Generic implements TabInterface
{
    protected $_systemStore;
    protected $status;
    protected $category;
    protected $storeManagerInterface;
    protected $magemobcartModel;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        \Biztech\Magemobcart\Model\Status $status,
        \Biztech\Magemobcart\Model\Category $category,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Biztech\Magemobcart\Model\Magemobcart $magemobcartModel,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_category = $category;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_registry = $registry;
        $this->_magemobcartModel = $magemobcartModel;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Featured Category Information');
    }

    public function getTabTitle()
    {
        return __('Featured Category Information');
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productImage = $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productImage = $productImage."Magemobcart/Magemobcart/";
        $registry = $this->_registry;
        $model = $this->_coreRegistry->registry('magemobcart_magemobcart_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Featured Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('magemobcart_id', 'hidden', ['name' => 'magemobcart_id']);
        }
        if ($model->getId()) {
            $beforeOrAfter = 'before_element_html';
        } else {
            $beforeOrAfter = 'after_element_html';
        }
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Category Title'),
                'title' => __('Category Title'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'is_showing',
            'select',
            [
                'label' => __('Show Category Title'),
                'title' => __('Show Category Title'),
                'name' => 'is_showing',
                'required' => true,
                'options' => array(
                    "0" => "No",
                    "1" => "Yes",
                ),
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'store_id',
            'multiselect',
            [
            'name'     => 'stores[]',
            'label'    => __('Store Views'),
            'title'    => __('Store Views'),
            'required' => true,
            'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $fieldset->addField(
            'filename',
            'file',
            [
            'name' => 'filename',
            'label' => __('Choose File'),
            'required' => $model->getData('filename') != "" ? false : true,
            'data-form-part' => $this->getData('target_form'),
            'note' => __('<b/>Note : </b> Allowed file types are jpg, jpeg & png and image size should be less than 5 mb.'),
            $beforeOrAfter => $this->_getWidgetSelectAfterHtml(),

            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $this->_status->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'category',
            'select',
            [
                'label' => __('Choose Category'),
                'title' => __('Choose Category'),
                'name' => 'category',
                // 'required' => true,
                'options' => $this->_category->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        if ($model) {
            $form->setValues($model->getData());
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    protected function _getWidgetSelectAfterHtml()
    {
        $model = $this->_coreRegistry->registry('magemobcart_magemobcart_data');
        $featuredModel = $this->_magemobcartModel->load($model->getId());
        $data = "";
        if (!empty($featuredModel->getData())) {
            $data .= "<img src=\"".$featuredModel->getFilepath()."\" width='100'>";
            $data .= "<br/>";
            $data .= "<br/>";
            return $data;
        }
        return $data;
    }
}
