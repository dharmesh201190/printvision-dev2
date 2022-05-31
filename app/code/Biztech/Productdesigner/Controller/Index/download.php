<?php


namespace Biztech\Productdesigner\Controller\Index;

use Magento\Framework\Archive\Zip as ZipArchive;
class download extends \Biztech\Productdesigner\Controller\Index {
   
    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $dataUrls = ($data['dataUrl']) ?: '';
            $product_image_data = (isset($data['product_image_data'])) ? $data['product_image_data'] : '';
            $previewImages = (isset($data['previewImages'])) ? $data['previewImages'] : 0;
            if($previewImages==1){
                $index = 0;
                foreach ($product_image_data as $data1 ) {
                   $product_image_path = $this->_infoHelper->convertRelToAbsPath($data1['url']);
                   $data1['path'] = $product_image_path;
                   $type = array('path' => 'download');
                   $allImages = $this->_infoHelper->generateImage($dataUrls[$index], $data1, $type);
                   $index++;
                   $valid_files[] = $allImages['base']['path'];
               }

               $zip = new \ZipArchive();
               $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
               $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
               $foldername = $reader .'productdesigner/download/zip';
               if(!is_dir($foldername)){
                 mkdir($foldername);
               }
               $zipName = $reader . 'productdesigner/download/zip/all_images.zip';
               if (count($valid_files)) {
                if (file_exists($zipName)) {
                    unlink($zipName);
                }
                $zip = new \ZipArchive();
                if ($zip->open($zipName, \ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                foreach ($valid_files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
            }
            $response['zipfile'] = $mediaUrl.'productdesigner/download/zip/all_images.zip';
        }
        else{
           $product_image_path = $this->_infoHelper->convertRelToAbsPath($product_image_data['url']);
           $product_image_data['path'] = $product_image_path;
           $type = array('path' => 'download');
           $response = $this->_infoHelper->generateImage($dataUrls, $product_image_data, $type);
           $response['status'] = 'success';
       }
       $this->getResponse()->setBody(json_encode($response));
   } catch (\Exception $e) {
    $response = $this->_infoHelper->throwException($e, self::class);
    $this->getResponse()->setBody(json_encode($response));
}
}

}
