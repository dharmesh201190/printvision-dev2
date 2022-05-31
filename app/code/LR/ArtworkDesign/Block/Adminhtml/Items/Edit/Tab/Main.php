<?php
namespace LR\ArtworkDesign\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Data\FormFactory $formFactory,  
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, 
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Item Information');
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
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_lr_artworkdesign_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('artworkdesign_id', 'hidden', ['name' => 'artworkdesign_id']);
        }
        $fieldset->addField(
            'product_name',
            'text',
            ['name' => 'artworkdesign_name', 'label' => __('Product Name'), 'title' => __('Product Name')]
        );
        $fieldset->addField(
            'artworkdesign_name',
            'text',
            ['name' => 'artworkdesign_name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
        $fieldset->addField(
            'artworkdesign_email',
            'text',
            ['name' => 'artworkdesign_email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );
        $fieldset->addField(
            'artworkdesign_phone',
            'text',
            ['name' => 'artworkdesign_phone', 'label' => __('Phone Number'), 'title' => __('Phone Number'), 'required' => true]
        );
        $fieldset->addType(
            'custom_type',
            '\LR\ArtworkDesign\Block\Adminhtml\Items\Edit\Renderer\Download'
        );

       $fieldset->addField(
            'artworkdesign_image',
            'custom_type',
            [
                'name' => 'artworkdesign_image',
                'label' => __('Download Artwork'),
                'title' => __('Download Artwork'),
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'artworkdesign_status',
            'select',
            ['name' => 'artworkdesign_status', 'label' => __('Status'), 'title' => __('Status'),  'options'   => [0 => 'Pending', 1 => 'Processing', 2 => 'Completed'], 'required' => true]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
