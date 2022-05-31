<?php
/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */
namespace Biztech\DesignTemplates\Block\Adminhtml\Designtemplatecategory\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('biztech_designtemplates_designtemplatecategory_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Design Templates Information')); 
        $this->addTabAfter(
                'templatecategory',
                [
                    'label' => __('Design Templates'),
                    'url' => $this->getUrl('*/*/templatecategory', ['_current' => true]),
                    'class' => 'ajax',
                    
                ],'main_section'
            );      
    }
}
