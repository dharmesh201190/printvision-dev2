<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\Productdesigner\Model\Mysql4;

class Designimages extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('productdesigner_design_images', 'image_id');
    }

}
