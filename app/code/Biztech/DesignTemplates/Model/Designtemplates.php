<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Model;

class Designtemplates extends \Magento\Framework\Model\AbstractModel {

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('Biztech\DesignTemplates\Model\Mysql4\Designtemplates');
    }

    public function getEncodedDesignId() {
        return rtrim(strtr(base64_encode($this->getId()), '+/', '-_'), '=');
    }

}
