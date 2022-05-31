<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDisable extends \Magento\Backend\App\Action
{
    
    protected $filter;
    
    protected $collectionFactory;
    protected $clipartFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Biztech\Productdesigner\Model\ClipartFactory $clipartFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->clipartFactory = $clipartFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $model = $this->clipartFactory->create();
            $model->load($item->getClipartId());
            $model->setStatus(2);
            $model->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been disabled.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
