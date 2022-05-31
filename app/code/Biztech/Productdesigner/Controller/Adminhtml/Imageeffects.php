<?php
namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Imageeffects extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;

    protected $resultForwardFactory;

    protected $resultPageFactory;

    protected $_fileUploaderFactory;
    protected $imageEffectsFactory;
    protected $session;
    protected $_filesystem;
    protected $logger;
  
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
         \Biztech\Productdesigner\Model\ImageeffectsFactory $imageEffectsFactory, \Magento\Backend\Model\Session $session, \Magento\Framework\Filesystem $filesystem, \Psr\Log\LoggerInterface $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploaderFactory = $uploaderFactory;
        $this->imageEffectsFactory = $imageEffectsFactory;
        $this->session = $session;
        $this->_filesystem = $filesystem;
        $this->logger = $logger;
        parent::__construct($context);
       
    }

    protected function _initAction()
    {   
        
        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::imageeffects')->_addBreadcrumb(__('Items'), __('Items'));
        
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::imageeffects');
    }
}
