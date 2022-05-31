<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Masking;

class Deletemaskingcategory extends \Magento\Framework\App\Action\Action
{
    protected $maskingCategoryFactory;
    protected $maskingFactory;
    protected $_filesystem;
    protected $maskingCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Biztech\Productdesigner\Model\MaskingcategoryFactory $maskingCategoryFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Biztech\Productdesigner\Model\Mysql4\Masking\CollectionFactory $maskingCollection,
        \Biztech\Productdesigner\Model\MaskingFactory $maskingFactory
    ) {
        $this->maskingCategoryFactory = $maskingCategoryFactory;
        $this->maskingCollection = $maskingCollection;
        $this->maskingFactory = $maskingFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($context);
    }
    
    public function execute()
    {   
        try {
            $postData = $this->getRequest()->getPostValue();
            $type = !empty($postData['type']) ? $postData['type'] : '';
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $path = $reader->getAbsolutePath() . 'productdesigner/masking/';

            if($type && $type == 'category'){
                $masking_category_id = (!empty($postData['masking_category_id'])) ? $postData['masking_category_id'] : '';

                $masking_data = $this->maskingCollection->create()->addFieldToFilter('masking_category_id', $masking_category_id);

                if(!empty($masking_data)){
                    foreach ($masking_data->getItems() as $key => $value) {
                        $masking_image_file = $path.$value['masking_path'];
                        if(file_exists($masking_image_file)){
                            unlink($masking_image_file);
                        }
                        $value->delete();
                    }
                }

                $masking_category_model = $this->maskingCategoryFactory->create();
                $masking_category_model->load($masking_category_id);
                $masking_category_model->delete();
                $masking_category_model->save();
            }

            if($type && $type == 'file'){
                $masking_id = (!empty($postData['masking_id'])) ? $postData['masking_id'] : '';
                $masking_model = $this->maskingFactory->create();
                $masking_model->load($masking_id);

                $masking_image_file = $path.$masking_model->getMaskingPath();
                if(file_exists($masking_image_file)){
                    unlink($masking_image_file);
                }

                $masking_model->delete();
                $masking_model->save();
            }

            $data['status'] = true;
            $this->getResponse()->setBody(json_encode($data));
        }catch (\Exception $e) {
            $result = ['status' => false, 'error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            $this->getResponse()->setBody(json_encode($result));
        }

    }
}