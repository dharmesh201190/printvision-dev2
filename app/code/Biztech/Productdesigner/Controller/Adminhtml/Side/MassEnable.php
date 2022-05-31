<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Side;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Side\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassEnable extends \Magento\Backend\App\Action
{
    protected $filter;
  
    protected $collectionFactory;
    protected $sideFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Biztech\Productdesigner\Model\SideFactory $sideFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->sideFactory = $sideFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $model = $this->sideFactory->create();
            $model->load($item->getImagesideId());
            $model->setStatus(1);
            $model->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been enabled.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
