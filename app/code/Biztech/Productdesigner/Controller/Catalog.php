<?php
namespace Biztech\Productdesigner\Controller;

abstract class Catalog extends \Magento\Framework\App\Action\Action {

    protected $_infoHelper;
    protected $_productLoader;
    protected $_storeManager;
    protected $_assetRepo;
    protected $_catalogCollectionFactory;
    protected $_pdHelper;
    protected $_categoryHelper;
    protected $_catalogModel;
    protected $_stockFilter;
    protected $_imagehelper;
    protected $allstore = array();

    public function __construct(
    	\Magento\Framework\App\Action\Context $context, 
    	\Biztech\Productdesigner\Helper\Info $infoHelper, 
    	\Magento\Catalog\Model\ProductFactory $_productLoader, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Framework\View\Asset\Repository $assetRepo, 
        \Biztech\Productdesigner\Helper\Data $dataHelper,
    	\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $catalogCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Category $catalogModel,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\Catalog\Helper\Image $imagehelper
        
    ) {
        $this->_infoHelper = $infoHelper;
        $this->_productLoader = $_productLoader;
        $this->_storeManager = $storeManager;
        $this->_assetRepo = $assetRepo;
        $this->_catalogCollectionFactory = $catalogCollectionFactory;
        $this->_pdHelper = $dataHelper;
        $this->_categoryHelper = $categoryHelper;
        $this->_catalogModel = $catalogModel;
        $this->_stockFilter = $stockFilter;
        $this->_imagehelper = $imagehelper;
        parent::__construct($context);
    }

}
