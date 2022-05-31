<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

use \Magento\Framework\View\LayoutFactory;

class viewDesign extends \Magento\Backend\App\Action {

    protected $resultPageFactory;

    protected $_layoutFactory;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_layoutFactory = $layoutFactory;
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('View Design'));
        return $resultPage;
    }

}
