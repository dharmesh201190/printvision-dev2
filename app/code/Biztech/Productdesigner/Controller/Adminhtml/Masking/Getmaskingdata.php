<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Masking;

class Getmaskingdata extends \Magento\Framework\App\Action\Action
{
    protected $maskingCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Biztech\Productdesigner\Model\Mysql4\Masking\CollectionFactory $maskingCollection
    ) {
        $this->maskingCollection = $maskingCollection;
        parent::__construct($context);
    }
    
    public function execute()
    {   
        try {
            $postData = $this->getRequest()->getPostValue();
            $masking_category_id = (!empty(trim($postData['masking_category_id']))) ? trim($postData['masking_category_id']) : '';
            
            $masking_data = $this->maskingCollection->create()->addFieldToFilter('masking_category_id', $masking_category_id)->getData();

            $data['masking_data'] = $masking_data;
            $data['status'] = true;
            $this->getResponse()->setBody(json_encode($data));
        }catch (\Exception $e) {
            $result = ['status' => false, 'error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            $this->getResponse()->setBody(json_encode($result));
        }

    }
}