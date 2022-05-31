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

class Save extends Action
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
     * Execute
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->isPost()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $jobModel = $this->jobFactory->create();
            if ($this->getRequest()->getParam('entity_id')) {
                $jobModel->load($this->getRequest()->getParam('entity_id'));
            }
            $params = $this->getRequest()->getParams();
            $jobModel->setData($params);
            $jobModel->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        $this->messageManager->addSuccess(__("Job saved successfully"));
        return $resultRedirect->setPath('*/*/');
    }
}
