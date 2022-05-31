<?php


namespace Biztech\Productdesigner\Controller\Adminhtml\Clipartmedia;

class Clipartmedia extends \Magento\Backend\App\Action {

    protected $resultPageFactory;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute() {

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->createBlock('Biztech\Productdesigner\Block\Adminhtml\Clipart\Gallery\Content')
                ->setTemplate('Biztech_Productdesigner::productdesigner/clipart/gallery/contentajax.phtml')
                ->toHtml();
        $this->getResponse()->setBody($block);
    }

}
