<?php
namespace Biztech\Productdesigner\Controller\Adminhtml;

abstract class Tabs extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;

    protected $resultForwardFactory;

    protected $resultPageFactory;
    protected $tabsCollection;
    protected $tabsFactory;
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Biztech\Productdesigner\Model\Mysql4\TabsData\CollectionFactory $tabsCollection, \Biztech\Productdesigner\Model\TabsDataFactory $tabsFactory, \Magento\Backend\Model\Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->tabsCollection = $tabsCollection;
        $this->tabsFactory  = $tabsFactory;
        $this->session = $session;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _initAction()
    {   
        $this->_view->loadLayout();
        $this->_setActiveMenu('Biztech_Productdesigner::tabs')->_addBreadcrumb(__('Tabs'), __('Tabs'));
        
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Biztech_Productdesigner::tabs');
    }
}
