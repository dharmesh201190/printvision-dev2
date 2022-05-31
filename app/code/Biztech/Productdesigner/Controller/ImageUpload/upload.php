<?php
namespace Biztech\Productdesigner\Controller\ImageUpload;

class upload extends \Magento\Framework\App\Action\Action {

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $data = array('status' => false);
            $this->getResponse()->setBody(json_encode($data));
        } else {
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $path = $reader->getAbsolutePath() . 'productdesigner/upload/';
            $resizedPath = $mediumPath = "";
            $fileData = $this->getRequest()->getFiles('file');
            if (isset($fileData['name'])) {
                if (!is_dir($reader->getAbsolutePath() . 'productdesigner')) {
                    mkdir($reader->getAbsolutePath() . 'productdesigner', 0777);
                }
                if (!is_dir($path)) {
                    mkdir($path, 0777);
                }
               if(pathinfo($fileData['name'], PATHINFO_EXTENSION) != 'svg'){
                    $resizedPath = $reader->getAbsolutePath() . 'productdesigner/upload/resized/';
                    $mediumPath = $reader->getAbsolutePath() . 'productdesigner/upload/medium/';
                    if (!is_dir($resizedPath)) {
                        mkdir($resizedPath, 0777);
                    }
                    if (!is_dir($mediumPath)) {
                        mkdir($mediumPath, 0777);
                    }
                }

                if (!is_writable($path) && !is_writable($resizedPath) && !is_writable($mediumPath)) {
                    $data = array(
                        'status' => false,
                        'msg' => 'Destination directory not writable.',
                    );
                    $this->getResponse()->setBody(json_encode($data));
                }

                $uploader = $this->setUploader($fileData);
                $result = $uploader->save(
                        $path
                );

                $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload' . $result['file'];
                $result['url'] = $url;
                $result['file'] = $result['file'];
                $result['state'] = 1;
                $filePath = $reader->getAbsolutePath() . 'productdesigner/upload'.$result['file'];

                if (!empty($result['file']) && $result['file']!="") {
                    $data ['success']= "image upload";
                  
                    // for device upload images
                    $exif = '';
                    if(exif_imagetype($filePath) == IMAGETYPE_JPEG) {
                        $string = file_get_contents($filePath);
                        $exif = exif_read_data("data:image/jpeg;base64," . base64_encode($string));
                    }
                    $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : '';
                    if (isset($orientation) && $orientation != 1) {
                        switch ($orientation) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if (isset($deg) && $deg) {
                            $img_new = imagecreatefromjpeg($filePath);
                            $img_new1 = imagerotate($img_new, $deg, 0);
                            imagejpeg($img_new1, $filePath, 80);
                        }
                    }

                    if (pathinfo($fileData['name'], PATHINFO_EXTENSION) != 'svg') {
                        $medium_width = $this->_helper::MediumImageWidth;
                        $resize_width= $this->_helper::ResizeImageWidth;
                        $this->_helper->resizeImage($path, $result,$resize_width,'resized');
                        $this->_helper->resizeImage($path, $result,$medium_width,'medium');
                        $data = array(
                            'status' => true,
                            'uniqueId' => $fileData['size'] . "-" . $fileData['name'],
                            'orientation' => $orientation,
                            'url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload/medium' . $result['file'],
                        );
                    } else {
                        $data = array(
                            'status' => true,
                            'orientation' => $orientation,
                            'uniqueId' => $fileData['size'] . "-" . $fileData['name'],
                            'url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload' . $result['file'],
                        );
                    }

                    $this->getResponse()->setBody(json_encode($data));
                }
            } else {
                $data = array('status' => false, 'msg' => 'No file uploaded.');
                $this->getResponse()->setBody(json_encode($data));
            }
        }
    }
    protected function setUploader($data) {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $data]);
        $uploader->setAllowedExtensions(["jpg", "jpeg", "svg", "png", "JPG", "JPEG", "SVG", "PNG"]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        return $uploader;
    }
}
