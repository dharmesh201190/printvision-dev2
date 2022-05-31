<?php
namespace LR\ArtworkDesign\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Comments extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
    protected $request;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Data\FormFactory $formFactory,  
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\App\Request\Http $request, 
        array $data = []
    ) 
    {
        $this->request = $request;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Comments');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Comments');
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
        echo $this->getLayout()
          ->createBlock('\LR\ArtworkDesign\Block\Comments')->setId($this->request->getParam('id'))
          ->setTemplate('LR_ArtworkDesign::comments.phtml')
          ->toHtml();
        $model = $this->_coreRegistry->registry('current_lr_artworkdesign_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Reply')]);
        if ($model->getId()) {
            $fieldset->addField('artworkdesign_id', 'hidden', ['name' => 'artworkdesign_id']);
        }
        $fieldset->addField(
            'admin_artwork_image',
            'image',
            [
                'name' => 'admin_artwork_image',
                'label' => __('Upload Updated Artwork'),
                'title' => __('Upload Updated Artwork'),
                'note' => __('Allow file type: svg, jpg, jpeg, png, docx, doc, pdf'),
                'required'  => false
            ]
        );
        $fieldset->addField(
            'admin_artwork_comment',
            'textarea',
            [
                'name' => 'admin_artwork_comment',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => true,
            ]
        );
       /* $fieldset->addField(
            'artworkdesign_comment',
            'editor',
            [
                'name' => 'artworkdesign_comment',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'style' => 'height:26em;',
                'required' => true,
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg' => true
            ]
        ); */
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
