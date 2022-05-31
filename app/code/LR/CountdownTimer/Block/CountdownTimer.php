<?php
namespace LR\CountdownTimer\Block;

class CountdownTimer extends \Magento\Framework\View\Element\Template
{
    protected $deliveryCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \LR\CountdownTimer\Model\ResourceModel\Delivery\CollectionFactory $deliveryCollectionFactory
    ) {
        parent::__construct($context);
        $this->deliveryCollectionFactory = $deliveryCollectionFactory;  
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('LR CountdownTimer Module'));
        
        return parent::_prepareLayout();
    }
    
    public function getDeliveryCollection()
    {
        $collection = $this->deliveryCollectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        $collection->getSelect()->order('sort_order ASC');
        return $collection;
    }
}
