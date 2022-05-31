<?php

namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Clipart extends \Magento\Backend\App\Action {

   
    protected $_coreRegistry;
    protected $_fileUploaderFactory;

    protected $resultForwardFactory;

    protected $resultPageFactory;
    protected $_imageFactory;
    protected $_helper;
    protected $clipartFactory;
    protected $clipartMediaCollection;
    protected $_filesystem;
    protected $_logger;
    protected $_clipartCollection;
    protected $clipartMediaFactory;
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Model\ClipartFactory $clipartFactory, \Biztech\Productdesigner\Model\ClipartmediaFactory $clipartMediaCollection, \Magento\Framework\Filesystem $filesystem, \Psr\Log\LoggerInterface $logger, \Biztech\Productdesigner\Model\Mysql4\Clipart\Collection $clipartCollection, \Biztech\Productdesigner\Model\ClipartmediaFactory $clipartMediaFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_imageFactory = $imageFactory;
        $this->_helper = $helper;
        $this->_fileUploaderFactory = $uploaderFactory;
         $this->clipartFactory = $clipartFactory;
        $this->clipartMediaCollection = $clipartMediaCollection;
        $this->_filesystem = $filesystem;
        $this->_logger = $logger;
        $this->_clipartCollection = $clipartCollection;
        $this->clipartMediaFactory = $clipartMediaFactory;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction() {

        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::clipart')->_addBreadcrumb(__('Items'), __('Items'));

        return $this;
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::clipart');
    }

}
