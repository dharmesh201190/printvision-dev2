<?php
namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Subtabs extends \Magento\Backend\App\Action
{
   
    protected $_coreRegistry;

    protected $resultForwardFactory;

    protected $resultPageFactory;
    protected $subtabsFactory;
    protected $subtabsCollection;
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Biztech\Productdesigner\Model\SubtabsFactory $subtabsFactory, \Biztech\Productdesigner\Model\Mysql4\Subtabs\CollectionFactory $subtabsCollection, \Magento\Backend\Model\Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->subtabsFactory =$subtabsFactory;
        $this->subtabsCollection = $subtabsCollection;
        $this->session = $session;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction()
    {   
        
        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::subtabs')->_addBreadcrumb(__('Items'), __('Items'));
        
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::subtabs');
    }
}
