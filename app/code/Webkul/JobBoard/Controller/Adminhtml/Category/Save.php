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

class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

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
            $categoryModel = $this->categoryFactory->create();
            if ($this->getRequest()->getParam('entity_id')) {
                $categoryModel->load($this->getRequest()->getParam('entity_id'));
            }
            $params = $this->getRequest()->getParams();
            $categoryModel->setName($params['name']);
            $categoryModel->setSort($params['sort']);
            $categoryModel->setStatus($params['status']);
            $categoryModel->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        $this->messageManager->addSuccess(__("Job Category saved successfully"));
        return $resultRedirect->setPath('*/*/');
    }
}
