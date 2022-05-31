<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Printablecolor;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Printablecolor\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDisable extends \Magento\Backend\App\Action
{
    protected $filter;
  
    protected $collectionFactory;
    protected $printableColorCollection;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Biztech\Productdesigner\Model\PrintablecolorFactory $printableColorCollection)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->printableColorCollection = $printableColorCollection;
        parent::__construct($context);
    }
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $model = $this->printableColorCollection->create();
            $model->load($item->getPrintablecolorId());
            $model->setStatus(2);
            $model->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been disabled.', $collectionSize));

        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
