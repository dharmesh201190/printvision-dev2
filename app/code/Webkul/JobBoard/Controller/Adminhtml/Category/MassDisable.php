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
use Magento\Ui\Component\MassAction\Filter;
use Webkul\JobBoard\Helper\Data;
use Webkul\JobBoard\Model\ResourceModel\Category\CollectionFactory;

class MassDisable extends Action
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
     * @param \Webkul\JobBoard\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;
    
    /**
     * @param Context $context
     * @param Filter $filter
     * @param Data $helper
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Data $helper,
        CollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->helper = $helper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Category MassDisable Action
     *
     * @return String
     */
    public function execute()
    {
        $categoryCollection = $this->filter->getCollection(
            $this->categoryCollectionFactory->create()
        );

        $count = 0;
        if ($categoryCollection->getSize()) {
            foreach ($categoryCollection as $category) {
                $category->setStatus(0);
                $this->helper->saveObject($category);
                $count++;
            }
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $count));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
