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
use Webkul\JobBoard\Model\CategoryFactory;

class Delete extends Action
{
    /**
     * @var \Webkul\JobBoard\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Context $context
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Category Delete Action
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        $jobBoardCategory = $this->categoryFactory->create()->load($categoryId);
        if (!($jobBoardCategory->getId())) {
            $this->messageManager->addError(__("Category doesn't exists."));
        }
        try {
            $jobBoardCategory->delete();
            $this->messageManager->addSuccess(__("Category deleted succesfully."));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Error occurred while deleting category."));
        }
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
