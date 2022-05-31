<?php
namespace Printvision\ProductInquiry\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Product Inquiries')));

        return $resultPage;
    }
}
