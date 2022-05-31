<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory;

class TemplatecategoryGrid extends \Biztech\DesignTemplates\Controller\Adminhtml\Designtemplatecategory {

    public function execute() {
        $resultLayout = $this->resultLayoutFactory->create();

        $resultLayout->getLayout()->getBlock('templatecategory.grid')->setInProducts($this->getRequest()->getPost('templates', null));
        return $resultLayout;
    }

}
