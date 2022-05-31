<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Masking;

class Createmaskingcategory extends \Magento\Framework\App\Action\Action
{
   
    protected $maskingCategoryFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Biztech\Productdesigner\Model\MaskingcategoryFactory $maskingCategoryFactory
    ) {
        $this->maskingCategoryFactory = $maskingCategoryFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {   
        try {
            $postData = $this->getRequest()->getPostValue();

            $masking_category_name = (!empty(trim($postData['masking_category_name']))) ? trim($postData['masking_category_name']) : '';
            $masking_category_model = $this->maskingCategoryFactory->create();
            $masking_arr=array(
                'masking_category_title' => $masking_category_name
            );
            $masking_category_model->setData($masking_arr);
            $masking_category_model->save();
            $getId = $masking_category_model->getId();
            $data['id'] = $getId;
            $data['status'] = true;
            $this->getResponse()->setBody(json_encode($data));
        }catch (\Exception $e) {
            $result = ['status' => false, 'error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            $this->getResponse()->setBody(json_encode($result));
        }

    }
}