<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Bannerslider\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class Bannerslider extends Generic implements TabInterface
{
    protected $_systemStore;
    protected $status;
    protected $product;
    protected $category;
    protected $bannersliderModel;
    protected $storeManager;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        \Biztech\Magemobcart\Model\Status $status,
        \Biztech\Magemobcart\Model\Product $product,
        \Biztech\Magemobcart\Model\Category $category,
        \Biztech\Magemobcart\Model\Bannerslider $bannersliderModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_product = $product;
        $this->_category = $category;
        $this->_bannersliderModel = $bannersliderModel;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    public function getTabLabel()
    {
        return __('Bannerslider Information');
    }
    public function getTabTitle()
    {
        return __('Bannerslider Information');
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
        $productImage = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productImage = $productImage."Magemobcart/Banners/";
        $registry = $this->_registry;
        $model = $this->_coreRegistry->registry('magemobcart_bannerslider_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Bannerslider Information')]);

        if ($model->getId()) {
            $fieldset->addField('bannerslider_id', 'hidden', ['name' => 'bannerslider_id']);
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
                'label' => __('Banner Title'),
                'title' => __('Banner Title'),
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
            'name' => 'filename', // to add reference later in controller.
            'label' => __('Choose File'),
            'required' => $model->getData('filename') != "" ? false : true,
            'data-form-part' => $this->getData('target_form'),
            'note' => __('<b/>Note : </b> Allowed file types are jpg, jpeg & png and image size should be less than 5 mb with 2 : 1 ratio.'),
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
        $type = $fieldset->addField(
            'type',
            'select',
            [
                'label' => __('Type'),
                'title' => __('Type'),
                'name' => 'type',
                'required' => true,
                'values' => array(
                    array(
                        'value' => __(''),
                        'label' => __('Select'),
                    ),
                    array(
                        'value' => 'category_offers',
                        'label' => __('Category'),
                    ),
                    array(
                        'value' => 'product',
                        'label' => __('Product'),
                    ),
                    array(
                        'value' => 'offer',
                        'label' => __('Offer'),
                    ),
                ),
                'disabled' => $isElementDisabled
            ]
        );
        $url = $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => true,
            ]
        );
        $product_id = $fieldset->addField(
            'product_id',
            'select',
            [
                'label' => __('Choose Product'),
                'title' => __('Choose Product'),
                'name' => 'product_id',
                // 'required' => true,
                'options' => $this->_product->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $category_id = $fieldset->addField(
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

        $this->setChild(
            'form_after',
            $dependence = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )
                        ->addFieldMap($type->getHtmlId(), $type->getName())
                        ->addFieldMap($url->getHtmlId(), $url->getName())
                        ->addFieldMap($category_id->getHtmlId(), $category_id->getName())
                        ->addFieldMap($product_id->getHtmlId(), $product_id->getName())
                        ->addFieldDependence(
                            $url->getName(),
                            $type->getName(),
                            'offer'
                        )
                        ->addFieldDependence(
                            $category_id->getName(),
                            $type->getName(),
                            'category_offers'
                        )
                        ->addFieldDependence(
                            $product_id->getName(),
                            $type->getName(),
                            'product'
                        )
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
        $model = $this->_coreRegistry->registry('magemobcart_bannerslider_data');
        $bannerModel = $this->_bannersliderModel->load($model->getId());
        $data = "";
        if (!empty($bannerModel->getData())) {
            $data .= "<img src=\"".$bannerModel->getFilepath()."\" width='100'>";
            $data .= "<br/>";
            $data .= "<br/>";
            return $data;
        }
        return $data;
    }
}
