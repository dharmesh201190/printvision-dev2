<?php
namespace Biztech\Productdesigner\Controller;

abstract class Index extends \Magento\Framework\App\Action\Action {
    protected $_infoHelper;
    protected $_productLoader;
    protected $resultPageFactory;
    protected $layoutFactory;
    protected $_pdHelper;
    protected $_storeManager;
    protected $scopeConfig;
    protected $session;
    protected $_filesystem;
    protected $cartModel;
    protected $quoteItemFactory;
    protected $orderItemRepository;
    protected $_objectFactory;
    protected $designFactory;
    protected $moduleReader;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Catalog\Model\ProductFactory $_productLoader, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Biztech\Productdesigner\Helper\Data $dataHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Filesystem $filesystem, \Magento\Checkout\Model\CartFactory $cartModel, \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory, \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository, \Magento\Framework\DataObjectFactory $objectFactory,
        \Biztech\Productdesigner\Model\DesignsFactory $designFactory, \Magento\Framework\Module\Dir\Reader $moduleReader
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
        $this->cartModel = $cartModel;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->_objectFactory = $objectFactory;
        $this->designFactory = $designFactory;
        $this->moduleReader = $moduleReader;
        
        parent::__construct($context);
    }

}
