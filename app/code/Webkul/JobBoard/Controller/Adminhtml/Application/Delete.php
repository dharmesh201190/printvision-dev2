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
use Webkul\JobBoard\Model\ApplicationFactory;

class Delete extends Action
{
    /**
     * @var \Webkul\JobBoard\Model\ApplicationFactory
     */
    protected $applicationFactory;

    /**
     * @param Context $context
     * @param ApplicationFactory $applicationFactory
     */
    public function __construct(
        Context $context,
        ApplicationFactory $applicationFactory
    ) {
        parent::__construct($context);
        $this->applicationFactory = $applicationFactory;
    }

    /**
     * Job Application Delete Action
     *
     * @return String
     */
    public function execute()
    {
        $applicationId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        $application = $this->applicationFactory->create()->load($applicationId);
        if (!($application->getId())) {
            $this->messageManager->addError(__("Job Application doesn't exists."));
        }
        try {
            $application->delete();
            $this->messageManager->addSuccess(__("Job Application deleted succesfully."));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Error occurred while deleting job application."));
        }
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
