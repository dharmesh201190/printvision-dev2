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

namespace Webkul\JobBoard\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Webkul\JobBoard\Model\CategoryFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $backendSession;

    /**
     * @var Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Webkul\JobBoard\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->backendSession = $context->getSession();
        $this->registry = $registry;
        $this->categoryFactory = $categoryFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $jobCategory = $this->categoryFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $jobCategoryId = $this->getRequest()->getParam('id');
            $jobCategory->load($jobCategoryId);
            $jobCategoryName = $jobCategory->getName();
            if (!$jobCategory->getId()) {
                $this->messageManager->addError(__('Item does not exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $jobCategory->setData($data);
        }
        
        $this->registry->register('jobboard_category', $jobCategory);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_JobBoard::categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Job Category'));
        $resultPage->getConfig()->getTitle()->prepend(isset($jobCategoryName) ? $jobCategoryName :
        __('New Job Category'));
        return $resultPage;
    }
}
