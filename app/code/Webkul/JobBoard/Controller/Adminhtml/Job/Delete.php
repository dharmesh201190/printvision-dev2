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
use Webkul\JobBoard\Model\JobFactory;

class Delete extends Action
{
    /**
     * @var \Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @param Context $context
     * @param JobFactory $jobFactory
     */
    public function __construct(
        Context $context,
        JobFactory $jobFactory
    ) {
        parent::__construct($context);
        $this->jobFactory = $jobFactory;
    }

    /**
     * Job Delete Action
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $jobId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        $job = $this->jobFactory->create()->load($jobId);
        if (!($job->getId())) {
            $this->messageManager->addError(__("Job doesn't exists."));
        }
        try {
            $job->delete();
            $this->messageManager->addSuccess(__("Job deleted succesfully."));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Error occurred while deleting job."));
        }
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
