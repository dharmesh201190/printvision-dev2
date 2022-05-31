<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\JobBoard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Webkul\JobBoard\Helper\Data;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Webkul\JobBoard\Helper\Data
     */
    protected $helper;
    
    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultPageFactory = $pageFactory;
    }

    /**
     * Index Execute Function
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        //$resultPage->getConfig()->getTitle()->prepend($this->helper->getJobBoardHeading());
        $resultPage->getConfig()->getTitle()->set($this->helper->getJobBoardHeading());
        return $resultPage;
    }
}
