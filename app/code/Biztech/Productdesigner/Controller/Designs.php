<?php
namespace Biztech\Productdesigner\Controller;

abstract class Designs extends \Magento\Framework\App\Action\Action {

    protected $_infoHelper;
    protected $_helper;
    protected $pdHelper;
    protected $_storeManager;
    protected $dir;
    protected $session;
    protected $designCollectionFactory;
    protected $_eventManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Catalog\Helper\Image $helper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Filesystem\DirectoryList $dir, \Biztech\Productdesigner\Helper\Data $pdHelper, \Magento\Customer\Model\Session $session, \Biztech\Productdesigner\Model\Mysql4\Designs\CollectionFactory $designCollectionFactory,
        \Magento\Framework\Event\Manager $manager
    ) {
        $this->_infoHelper = $infoHelper;
        $this->dir = $dir;
        $this->_helper = $helper;
        $this->pdHelper = $pdHelper;
        $this->_storeManager = $storeManager; 
        $this->session = $session;    
        $this->designCollectionFactory = $designCollectionFactory;   
        $this->_eventManager = $manager;
        parent::__construct($context);
    }

}
