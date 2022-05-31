<?php
namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Fonts extends \Magento\Backend\App\Action
{
   
    protected $_coreRegistry;
    protected $_fileUploaderFactory;

    protected $resultForwardFactory;

    
    protected $resultPageFactory;
    protected $_filesystem;
    protected $fontsFactory;
    protected $logger;
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, \Magento\Framework\Filesystem $filesystem, \Biztech\Productdesigner\Model\FontsFactory $fontsFactory, \Psr\Log\LoggerInterface $logger, \Magento\Backend\Model\Session $session
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileUploaderFactory = $uploaderFactory;
        $this->_filesystem = $filesystem;
        $this->fontsFactory = $fontsFactory;
        $this->logger = $logger;
        $this->session = $session;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction()
    {

        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::shapes')->_addBreadcrumb(__('Items'), __('Items'));

        return $this;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::fonts');
    }
}
