<?php

namespace Biztech\Productdesigner\Controller\Cart;

class uploadCustomOptionFile extends \Magento\Framework\App\Action\Action {

    protected $_fileUploaderFactory;
    protected $__uploader;
    protected $resultRawFactory;
    protected $_imageFactory;
    protected $_filesystem;
    protected $_storeManager;
    protected $_helper;
    protected $_infoHelper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\File\UploaderFactory $fileUploaderFactory, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->resultRawFactory = $resultRawFactory;
        $this->_filesystem = $filesystem;
        $this->_helper = $helper;
        $this->_infoHelper = $infoHelper;
        parent::__construct($context);
    }
    public function execute() {
        try {
            $allowedExtensions = explode(",", $this->getRequest()->getParams()['allowedExtensions']);
            $data = $this->getRequest()->getFiles('file');
            $uploader = $this->setUploader($data, $allowedExtensions);
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $uploadDir = 'productdesigner/CustomOptionFiles';

            $prod_image_dir = $reader->getAbsolutePath() . $uploadDir;

            if (!file_exists($prod_image_dir)) {
                mkdir($prod_image_dir, 0777);
            }
            $result = $uploader->save(
                    $prod_image_dir
            );
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $uploadDir . $result['file'];
            $result['url'] = $url;
            $result['file'] = $result['file'];
            $result['state'] = 1;

            $path = $prod_image_dir . "/";
            $response = $this->resultRawFactory->create();
            $response->setHeader('Content-type', 'text/plain');
            $response->setContents(json_encode($result));
            return $response;
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    protected function setUploader($data, $allowedExtensions) {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
        $uploader->setAllowedExtensions($allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        return $uploader;
    }

}
