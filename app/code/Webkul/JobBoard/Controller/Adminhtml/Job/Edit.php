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
namespace Webkul\JobBoard\Controller\Adminhtml\Job;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Webkul\JobBoard\Model\JobFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param JobFactory $jobFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        JobFactory $jobFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->backendSession = $context->getSession();
        $this->registry = $registry;
        $this->jobFactory = $jobFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $jobModel = $this->jobFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $jobModel->load($this->getRequest()->getParam('id'));
            if (!$jobModel->getId()) {
                $this->messageManager->addError(__('Item does not exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $jobModel->setData($data);
        }
        
        $this->registry->register('jobboard_job', $jobModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_JobBoard::jobs');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Job Details'));
        $resultPage->getConfig()->getTitle()->prepend(
            $jobModel->getId() ? $jobModel->getTitle() : __('Create New Job')
        );
        return $resultPage;
    }
}
