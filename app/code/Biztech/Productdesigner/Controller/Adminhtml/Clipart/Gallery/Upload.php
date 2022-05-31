<?php
namespace Biztech\Productdesigner\Controller\Adminhtml\Clipart\Gallery;

class Upload extends \Magento\Framework\App\Action\Action {

    const Thumbnail = 90;
    protected $_fileUploaderFactory;
    protected $__uploader;
    protected $resultRawFactory;
    protected $_imageFactory;
    protected $_filesystem;
    protected $_storeManager;
    protected $_helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Framework\File\UploaderFactory $fileUploaderFactory, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_filesystem = $filesystem;
        $this->_helper = $helper;
    }
    public function execute() {
        try {
            $data = $this->getRequest()->getFiles();

            $uploader = $this->setUploader($data);
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $prod_image_dir = $reader->getAbsolutePath() . 'productdesigner/clipart';
            $result = $uploader->save(
                $prod_image_dir
            );
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/clipart' . $result['file'];
            $result['url'] = $url;
            $result['file'] = $result['file'];
            $result['state'] = 1;

            /*
             * Added to resize
             */
            $path = $prod_image_dir . "/";
            $this->saveImage($path, $result);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    protected function setUploader($data) {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $data['clipart-image']]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'svg']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        return $uploader;
    }

    protected function saveImage($path, $result) {
        $this->_helper->resizeImage($path, $result, self::Thumbnail, 'resized');
    }

}
