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
use Magento\Ui\Component\MassAction\Filter;
use Webkul\JobBoard\Helper\Data;
use Webkul\JobBoard\Model\ResourceModel\Application\CollectionFactory;

class MassDelete extends Action
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
     * @param \Webkul\JobBoard\Model\ResourceModel\Application\CollectionFactory
     */
    protected $applicationCollectionFactory;
    
    /**
     * @param Context $context
     * @param Filter $filter
     * @param Data $helper
     * @param CollectionFactory $applicationCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Data $helper,
        CollectionFactory $applicationCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->helper = $helper;
        $this->applicationCollectionFactory = $applicationCollectionFactory;
    }

    /**
     * Application MassDelete Action
     *
     * @return void
     */
    public function execute()
    {
        $applicationCollection = $this->filter->getCollection(
            $this->applicationCollectionFactory->create()
        );

        $count = 0;
        if ($applicationCollection->getSize()) {
            foreach ($applicationCollection as $application) {
                $this->helper->deleteObject($application);
                $count++;
            }
        }
        $this->messageManager
            ->addSuccess(__('A total of %1 record(s) have been deleted.', $count));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
