<?php

namespace Biztech\Productdesigner\Controller;

abstract class Tabs extends \Magento\Framework\App\Action\Action {

  
    protected $_infoHelper;
    protected $_productLoader;
    protected $resultPageFactory;
    protected $layoutFactory;
    protected $_pdHelper;
    protected $_storeManager;
    protected $_maintabsdata;
    protected $_subtabsdata;
    protected $_resource;
    protected $_objectFactory;
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Catalog\Model\ProductFactory $_productLoader, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\LayoutFactory $layoutFactory,\Biztech\Productdesigner\Helper\Data $dataHelper,\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Biztech\Productdesigner\Model\Mysql4\Subtabs\CollectionFactory $SubtabsData,
        \Biztech\Productdesigner\Model\Mysql4\TabsData\CollectionFactory $MaintabsData,
        \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\DataObjectFactory $objectFactory

    ) {
        $this->_infoHelper = $infoHelper;
        $this->_productLoader = $_productLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_pdHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->_maintabsdata = $MaintabsData;
        $this->_subtabsdata = $SubtabsData;
        $this->_resource = $resource;  
        $this->_objectFactory = $objectFactory;      
        parent::__construct($context);
    }

}
