<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller;

/**
 * Items controller
 */
abstract class Designtemplates extends \Magento\Framework\App\Action\Action {

    protected $templateHelper;
    protected $_storeManager;
    protected $templateCategoryFactory;
    protected $_productLoader;
    protected $_infoHelper;
    protected $_objectFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ProductFactory $_productLoader, \Biztech\DesignTemplates\Helper\Template $templateHelper, \Biztech\DesignTemplates\Model\Mysql4\Designtemplatecategory\CollectionFactory $templateCategoryFactory, \Biztech\Productdesigner\Helper\Info $infoHelper,\Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->templateHelper = $templateHelper;
        $this->_storeManager = $storeManager;
        $this->_productLoader = $_productLoader;
        $this->templateCategoryFactory = $templateCategoryFactory;
        $this->_infoHelper = $infoHelper;
        $this->_objectFactory = $objectFactory;
        parent::__construct($context);
    }

}
