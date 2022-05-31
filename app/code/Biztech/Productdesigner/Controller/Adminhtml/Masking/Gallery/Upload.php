<?php
/**
 *
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */
namespace Biztech\Productdesigner\Controller\Adminhtml\Masking\Gallery;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_fileUploaderFactory;
    protected $__uploader;
    protected $resultRawFactory;
    protected $_filesystem;
    protected $maskingCollection;
    protected $maskingFactory;
    protected $_storeManager;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\Filesystem $filesystem, \Biztech\Productdesigner\Model\Mysql4\Masking\CollectionFactory $maskingCollection, \Biztech\Productdesigner\Model\MaskingFactory $maskingFactory, \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->maskingCollection = $maskingCollection;
        $this->maskingFactory = $maskingFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
    }
    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {   
        try {
         $fileData = $this->getRequest()->getFiles('file');
         $postData = $this->getRequest()->getPostValue();

         $maskingCollection = $this->maskingCollection->create();
         $label = (!empty(trim($postData['label']))) ? trim($postData['label']) : '';
         $masking_category_id = (!empty($postData['category_id'])) ? $postData['category_id'] : 0;


         $chknameConflicts = false;
        /* $i=0;
         while ($chknameConflicts == false && sizeof($maskingCollection->getData()) > 0) {
                $data = $maskingCollection->getData()[$i];
                if($data['masking_label'] == $label){
                    $chknameConflicts = true;
                }
                $i++;
         }*/
        foreach ($maskingCollection->getData() as $key => $value) {
            if($value['masking_label'] == $label && $chknameConflicts == false){
                $chknameConflicts = true;
            }
        }
        if($chknameConflicts){
            $data = array('status' => false, 'error' => 'Name already exist !');
            return $this->getResponse()->setBody(json_encode($data));            
        }

        if (isset($fileData['name'])) {
         $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
         $directory = $reader . 'productdesigner/masking';
         if (!is_dir($reader. 'productdesigner')) {
            mkdir($reader. 'productdesigner', 0777);
        }
        if (!is_dir($directory)) {
            mkdir($directory, 0777);                                
        }
        if (!is_writable($directory)) {
            $data = array(
                'status' => false,
                'msg' => 'Destination directory not writable.',
            );
            $this->getResponse()->setBody(json_encode($data));
        }
        /*Upload Image*/
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $fileData]);
        $uploader->setAllowedExtensions(['SVG','svg']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $result = $uploader->save($directory);
        if(sizeof($result) > 0 && $result['error'] == 0){

            /*Save Data in DB*/
            $masking_model = $this->maskingFactory->create();

            $masking_arr=array(
                'masking_path' => $result['file'],
                'masking_label' => $label,
                'masking_category_id' => $masking_category_id
            );
            $masking_model->setData($masking_arr);
            $masking_model->save();
            $getId = $masking_model->getId();

            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/masking' . $result['file'];
            $data['url'] = $url;
            $data['status'] = true;
            $data['label'] = $label;
            $data['id'] = $getId;
            $this->getResponse()->setBody(json_encode($data));
        }else{
           $data = array('status' => false, 'error' => "Something wen't to wrong!");
           $this->getResponse()->setBody(json_encode($data));
       }   
   }else{
    $data = array('status' => false, 'error' => 'No file uploaded.');
    $this->getResponse()->setBody(json_encode($data));
}
}catch (\Exception $e) {
    $result = ['status' => false, 'error' => $e->getMessage(), 'errorcode' => $e->getCode()];
    $this->getResponse()->setBody(json_encode($result));
}

}
}