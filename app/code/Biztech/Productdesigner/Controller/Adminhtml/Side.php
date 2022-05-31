<?php


namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Side extends \Magento\Backend\App\Action
{
   
    protected $_coreRegistry;

   
    protected $resultForwardFactory;

    protected $resultPageFactory;
    protected $sideFactory;
    protected $logger;
    protected $session;
    protected $sideCollection;
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Biztech\Productdesigner\Model\SideFactory $sideFactory, \Psr\Log\LoggerInterface $logger, \Magento\Backend\Model\Session $session, \Biztech\Productdesigner\Model\Mysql4\Side\CollectionFactory $sideCollection
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->sideFactory = $sideFactory;
        $this->logger = $logger;
        $this->session = $session;
        $this->sideCollection = $sideCollection;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction()
    {   
        
        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::side')->_addBreadcrumb(__('Items'), __('Items'));
        
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::side');
    }
}
