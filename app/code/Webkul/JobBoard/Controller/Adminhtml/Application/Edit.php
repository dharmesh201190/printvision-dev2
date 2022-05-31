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
namespace Webkul\JobBoard\Controller\Adminhtml\Application;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Webkul\JobBoard\Model\ApplicationFactory;
use Webkul\JobBoard\Model\JobFactory;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\JobBoard\Model\ApplicationFactory
     */
    protected $applicationFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param ApplicationFactory $applicationFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        ApplicationFactory $applicationFactory
    ) {
        parent::__construct($context);
        $this->backendSession = $context->getSession();
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->applicationFactory = $applicationFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $applicationModel = $this->applicationFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $applicationModel->load($this->getRequest()->getParam('id'));
            if (!$applicationModel->getId()) {
                $this->messageManager->addError(__('Item does not exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $backendSessionData = $this->backendSession->getFormData(true);
        if (!empty($backendSessionData)) {
            $applicationModel->setData($backendSessionData);
        }
        
        $this->registry->register('jobboard_application', $applicationModel);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_JobBoard::Application');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Application Details'));
        $resultPage->getConfig()->getTitle()->prepend(
            $applicationModel->getId() ?
            $applicationModel->getFirstname()." ".$applicationModel->getLastname() : __('Create New Job Application')
        );
        return $resultPage;
    }
}
