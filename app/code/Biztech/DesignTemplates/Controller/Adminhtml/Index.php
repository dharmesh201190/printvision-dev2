<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\DesignTemplates\Controller\Adminhtml;

/**
 * Items controller
 */
abstract class Index extends \Magento\Backend\App\Action {

    /**
     * @var \Biztech\Productdesigner\Helper\Info
     */
    protected $_infoHelper;
    protected $_productLoader;
    protected $resultPageFactory;
    protected $layoutFactory;
    protected $_pdHelper;
    protected $_storeManager;
    protected $scopeConfig;
    protected $session;
    protected $_filesystem;
    protected $templateHelper;
    protected $templateCategoryCollection;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Biztech\Productdesigner\Helper\Info $infoHelper
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Catalog\Model\ProductFactory $_productLoader, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Biztech\Productdesigner\Helper\Data $dataHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Filesystem $filesystem, \Biztech\DesignTemplates\Helper\Template $templateHelper, \Biztech\DesignTemplates\Model\Mysql4\Designtemplatecategory\CollectionFactory $templateCategoryCollection
    ) {
        $this->_infoHelper = $infoHelper;
        $this->_productLoader = $_productLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_pdHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->session = $customerSession;
        $this->_filesystem = $filesystem;
        $this->templateHelper = $templateHelper;
        $this->templateCategoryCollection = $templateCategoryCollection;
        parent::__construct($context);
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Biztech_DesignTemplates::designtemplatecategory');
    }

}
