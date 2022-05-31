<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Fonts;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Biztech\Productdesigner\Model\Mysql4\Fonts\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassEnable extends \Magento\Backend\App\Action
{
    
    protected $filter;
   
    protected $collectionFactory;
    protected $fontsFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Biztech\Productdesigner\Model\FontsFactory $fontsFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->fontsFactory = $fontsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $model = $this->fontsFactory->create();
            $model->load($item->getFontsId());
            $model->setStatus(1);
            $model->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been enabled.', $collectionSize));

        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
