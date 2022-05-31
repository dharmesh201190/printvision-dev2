<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Offerslider\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return Void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('offerslider_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Offer Slider'));
    }
}
