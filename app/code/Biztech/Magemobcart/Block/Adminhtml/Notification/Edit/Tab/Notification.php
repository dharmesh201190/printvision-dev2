<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Notification\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class Notification extends Generic implements TabInterface
{

    /**
     * @var Store
     */
    protected $_systemStore;
    protected $status;
    protected $product;
    protected $category;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        \Biztech\Magemobcart\Model\Status $status,
        \Biztech\Magemobcart\Model\Product $product,
        \Biztech\Magemobcart\Model\Category $category,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_product = $product;
        $this->_category = $category;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Notification Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Notification Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productImage = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productImage = $productImage."Magemobcart/Banners/";
        $registry = $objectManager->get('Magento\Framework\Registry');
        $model = $this->_coreRegistry->registry('magemobcart_notification_data');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        //$form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Notification Information')]);

        if ($model->getId()) {
            $fieldset->addField('notification_id', 'hidden', ['name' => 'notification_id']);
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
                'label' => __('Notification Title'),
                'title' => __('Notification Title'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'filename',
            'file',
            [
            'name' => 'filename',
            'label' => __('Choose File'),
            'required' => false,
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
            'category_id',
            'select',
            [
                'label' => __('Choose Category'),
                'title' => __('Choose Category'),
                'name' => 'category_id',
                // 'required' => true,
                'options' => $this->_category->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
                'label' => __('Notification Message'),
                'title' => __('Notification Message'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'os',
            'select',
            [
                'label' => __('Choose Os'),
                'title' => __('Choose Os'),
                'name' => 'os',
                'required' => true,
                'values' => array(
                    array(
                        'value' => 'all',
                        'label' => __('Both'),
                    ),
                    array(
                        'value' => 'android',
                        'label' => __('Android'),
                    ),
                    array(
                        'value' => 'ios',
                        'label' => __('iOS'),
                    ),
                ),
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

    /**
    * Check permission for passed action
    *
    * @param string $resourceId
    * @return bool
    */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    protected function _getWidgetSelectAfterHtml()
    {
        $model = $this->_coreRegistry->registry('magemobcart_notification_data');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $notificationModel = $objectManager->get('Biztech\Magemobcart\Model\Notification')->load($model->getId());
        $data = "";
        if (!empty($notificationModel->getData())) {
            $data .= "<img src=\"".$notificationModel->getFilepath()."\" width='100'>";
            $data .= "<br/>";
            $data .= "<br/>";
            return $data;
        }
        return $data;
    }
}
