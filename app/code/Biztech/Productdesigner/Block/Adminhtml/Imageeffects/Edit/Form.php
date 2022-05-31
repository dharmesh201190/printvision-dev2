<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Imageeffects\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_imageeffects_form');
        $this->setTitle(__('Image Effects and Filters'));
    }

    protected function _prepareForm()
    {
       
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('productdesigner/imageeffects/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
