<?php


namespace Biztech\Productdesigner\Controller\Index;

class delete extends \Magento\Framework\App\Action\Action {


    protected $_storeManager;
    protected $_helper;
    protected $_fileUploaderFactory;
    protected $_filesystem;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager,\Biztech\Productdesigner\Helper\Data $helper, \Magento\Framework\File\UploaderFactory $fileUploaderFactory, \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute() {
        
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $getExtension = explode(".", $data['url']);
            $extension = $getExtension[count($getExtension)-1];
            if($extension!='svg'){
                $getDirectory = explode("/", $data['url'], 9);            
            }else{
                $getDirectory = explode("/", $data['url'], 8);
            }

            $directoryPath=$getDirectory[count($getDirectory)-1];
            
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $path = $reader->getAbsolutePath() . 'productdesigner/upload/';
            $baseImg = $path . $directoryPath;
            $resizedImg = $path . 'resized/' . $directoryPath;
            $mediumImg = $path . 'medium/' . $directoryPath;

            if (file_exists($baseImg)) {
                unlink($baseImg);
            }
            if($extension!='svg'){
                if (file_exists($resizedImg)) {
                    unlink($resizedImg);
                }
                if (file_exists($mediumImg)) {
                    unlink($mediumImg);
                }
                $this->_helper->checkForDirectory($baseImg);
                $this->_helper->checkForDirectory($resizedImg);
                $this->_helper->checkForDirectory($mediumImg);
            }
            $data = array('status' => true,"message"=>"file deleted successfully");
            $this->getResponse()->setBody(json_encode($data));
    }
}
