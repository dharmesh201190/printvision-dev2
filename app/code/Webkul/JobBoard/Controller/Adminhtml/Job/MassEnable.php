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
use Magento\Ui\Component\MassAction\Filter;
use Webkul\JobBoard\Helper\Data;
use Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory;

class MassEnable extends Action
{
    /**
     * @param \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @param \Webkul\JobBoard\Helper\Data
     */
    protected $helper;

    /**
     * @param \Webkul\JobBoard\Model\ResourceModel\Job\CollectionFactory
     */
    protected $jobCollectionFactory;
    
    /**
     * @param Context $context
     * @param Filter $filter
     * @param Data $helper
     * @param CollectionFactory $jobCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Data $helper,
        CollectionFactory $jobCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->helper = $helper;
        $this->jobCollectionFactory = $jobCollectionFactory;
    }

    /**
     * Job MassEnable Action
     *
     * @return \Magento\Framework\Controller\ResultFactory
     */
    public function execute()
    {
        $jobCollection = $this->filter->getCollection(
            $this->jobCollectionFactory->create()
        );

        $count = 0;
        if ($jobCollection->getSize()) {
            foreach ($jobCollection as $job) {
                $job->setStatus(1);
                $this->helper->saveObject($job);
                $count++;
            }
        }
        $this->messageManager
            ->addSuccess(__('A total of %1 record(s) have been enabled.', $count));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
