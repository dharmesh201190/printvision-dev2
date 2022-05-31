<?php

namespace Biztech\Productdesigner\Controller\Index;

class upload extends \Magento\Framework\App\Action\Action {

   
    protected $_storeManager;
    protected $_helper;
    protected $_fileUploaderFactory;
    protected $_filesystem;
    protected $_customerSession;
    protected $_customerImages;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Biztech\Productdesigner\Helper\Data $helper, 
        \Magento\Framework\File\UploaderFactory $fileUploaderFactory, 
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Session $customerSession,
        \Biztech\Productdesigner\Model\Customerimages $customerImages
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_customerSession = $customerSession;
        $this->_customerImages = $customerImages;
        parent::__construct($context);
    }

    public function execute() {        
        $data = json_decode(file_get_contents('php://input'), TRUE);
        if (!empty($data['action']) && $data['action'] == "delete") {
            $getExtension = explode(".", $data['url']);
            $extension = $getExtension[count($getExtension) - 1];
            if ($extension != 'svg') {
                $getDirectory = explode("/", $data['url'], 9);
            } else {
                $getDirectory = explode("/", $data['url'], 8);
            }

            $directoryPath = $getDirectory[count($getDirectory) - 1];
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $path = $reader->getAbsolutePath() . 'productdesigner/upload/';
            $baseImg = $path . $directoryPath;
            $resizedImg = $path . 'resized/' . $directoryPath;
            $mediumImg = $path . 'medium/' . $directoryPath;

            if (file_exists($baseImg)) {
                unlink($baseImg);
            }

            if ($extension != 'svg') {
                if (file_exists($resizedImg)) {
                    unlink($resizedImg);
                }
                if (file_exists($mediumImg)) {
                    unlink($mediumImg);
                }
            }

            if ($this->_customerSession->isLoggedIn()) {
                $customerImageId = $data['id'];
                $customerImagesModel = $this->_customerImages->load($customerImageId);
                $this->_customerImages->delete();
                $this->_customerImages->save();
            }

            $data = array('status' => true, "message" => "file deleted successfully");
            $this->getResponse()->setBody(json_encode($data));
        } else {
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

                    if (pathinfo($fileData['name'], PATHINFO_EXTENSION) != 'svg') {
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
                    $filePath = $reader->getAbsolutePath() . 'productdesigner/upload' . $result['file'];

                    if (!empty($result['file']) && $result['file'] != "") {
                        $id = 0;
                        if ($this->_customerSession->isLoggedIn()) {
                            $customerData = $this->_customerSession->getCustomer();
                            $customer_id = $customerData->getId();
                            $customerImagesModel = $this->_customerImages;
                            $customerImagesModel->setCustomerId($customer_id);
                            $customerImagesModel->setImagePath($result['file']);
                            $customerImagesModel->save();
                            $id = $customerImagesModel->getId();
                        }
                        $data ['success'] = "image upload";

                        $string = file_get_contents($filePath);
                        
                            // for device upload images
                        $exif = '';
                        if (exif_imagetype($filePath) == IMAGETYPE_JPEG) {
                            $exif = exif_read_data("data://image/jpeg;base64," . base64_encode($string));
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
                            $resize_width = \Biztech\Productdesigner\Helper\Data::ResizeImageWidth;
                            $this->_helper->resizeImage($path, $result, $resize_width, 'resized');
                            $data = array(
                                'status' => true,
                                'id' => $id,
                                'uniqueId' => $fileData['size'] . "-" . $fileData['name'],
                                'orientation' => $orientation,
                                'url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload/resized' . $result['file'],
                                'medium_url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload' . $result['file'],
                            );
                        } else {
                            $data = array(
                                'status' => true,
                                'id' => $id,
                                'orientation' => $orientation,
                                'uniqueId' => $fileData['size'] . "-" . $fileData['name'],
                                'url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload' . $result['file'],
                                'medium_url' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload' . $result['file'],
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
    }

    protected function setUploader($data) {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $data]);
        $uploader->setAllowedExtensions(["jpg", "jpeg", "svg", "png", "JPG", "JPEG", "SVG", "PNG"]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        return $uploader;
    }

    protected function checkForDirectory($directoryPath, $baseDirectoryName = 'clipart') {

        // flag value that will be changed once operation is done
        $flag = false;

        // Process begins
        do {

            // Store last position of directory separator
            $pos = strrpos($directoryPath, '/');

            // fetch full path till directory of image
            $directoryPath = substr($directoryPath, 0, $pos);

            // find last occurence of directory separator
            $pos = strrpos($directoryPath, '/') + 1;

            // find directory name
            $pathName = substr($directoryPath, $pos);

            // Check if directory is 
            if (is_dir($directoryPath) && ($pathName != $baseDirectoryName && $pathName != 'resized')) {

                // check for available files(excluding system hidden folders)
                $files = array_diff(scandir($directoryPath), array('..', '.'));

                // if file count is 0, that indicates folder is empty, will remove the same
                if (count($files) == 0) {
                    rmdir($directoryPath);
                }
                $flag = false;
                // check if path has reached to base image path which indicates that our operation is complete
            } else if (($pathName == $baseDirectoryName || $pathName == 'resized')) {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            } else if ($pathName == $baseDirectoryName) {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            }
        } while ($flag == false);
    }

}
