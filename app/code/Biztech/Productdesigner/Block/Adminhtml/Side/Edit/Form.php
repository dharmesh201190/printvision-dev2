<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Side\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_side_form');
        $this->setTitle(__('Image Side'));
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('productdesigner/side/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
