<?php
namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Printablecolor extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;

   
    protected $resultForwardFactory;

   
    protected $resultPageFactory;
    protected $printableColorCollection;
    protected $printableColor;
    protected $logger;
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Biztech\Productdesigner\Model\PrintablecolorFactory $printableColorCollection, \Psr\Log\LoggerInterface $logger, \Magento\Backend\Model\Session $session, \Biztech\Productdesigner\Model\Mysql4\Printablecolor\CollectionFactory $printableColor
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->printableColorCollection = $printableColorCollection;
        $this->logger = $logger;
        $this->printableColor = $printableColor;
        $this->session  =$session;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction()
    {   
        
        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::printablecolor')->_addBreadcrumb(__('Items'), __('Items'));
        
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::printablecolor');
    }
}
