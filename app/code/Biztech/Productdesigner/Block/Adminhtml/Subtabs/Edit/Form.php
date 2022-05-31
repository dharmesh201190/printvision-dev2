<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Subtabs\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {
    protected function _construct() {
        parent::_construct();
        $this->setId('biztech_subtabs_form');
        $this->setTitle(__('Subtabs Information'));
    }
    protected function _prepareForm() {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'edit_form',
                        'action' => $this->getUrl('productdesigner/subtabs/save'),
                        'method' => 'post',
                    ],
                ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
