<?php

namespace Biztech\Watermark\Observer;

class WatermarkImages implements \Magento\Framework\Event\ObserverInterface
{
    protected $_filesystem;
    protected $_eventManager;
    protected $_scopeConfig;
    protected $_helper;
    protected $_imageFactory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,  \Magento\Framework\Event\Manager $manager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Biztech\Productdesigner\Helper\Data $helper, \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_eventManager = $manager;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_imageFactory = $imageFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
      $destination = $observer->getData('destination');
      $generatedImageFullPath =$observer->getData('generatedImageFullPath');
      $imgtype = $observer->getData('imgtype');
      $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
      $text = $this->_scopeConfig->getValue('productdesigner/downloaddesign_general/watermark_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      $image = $this->_scopeConfig->getValue('productdesigner/downloaddesign_general/watermark', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      
      if (( $text != null) || ($text != '')) {
        $logo = $reader . 'productdesigner/uploadwatermark/default/textwatermark.png';
        if (file_exists($logo)) {
            if (getimagesize($logo)) {
                $info = getimagesize($logo);
                $destinationsx = imagesx($destination);
                $destinationsy = imagesy($destination);
                $imgtype = image_type_to_mime_type($info[2]);
                switch ($imgtype) {
                    case 'image/jpeg':
                    $src = imagecreatefromjpeg($logo);
                    break;
                    case 'image/gif':
                    $src = imagecreatefromgif($logo);
                    break;
                    case 'image/png':
                    $percent = 1;
                    list($width, $height) = getimagesize($logo);
                    $newwidth = $width * $percent;
                    $newheight = $height * $percent;
                    $thumb = imagecreate($newwidth, $newheight);
                    // $imageResize = $this->_imageFactory->create();
                    // $imageResize->open($logo);
                    // $imageResize->constrainOnly(TRUE);
                    // $imageResize->keepTransparency(TRUE);
                    // $imageResize->keepFrame(FALSE);
                    // $imageResize->keepAspectRatio(FALSE);
                    // $imageResize->resize($destinationsx, $destinationsy);
                    // $imageResize->save($reader . 'productdesigner/uploadwatermark/resized/textwatermark.png');
                    // $logo = $reader . 'productdesigner/uploadwatermark/resized/textwatermark.png';
                    // $logo = imagecreatetruecolor($destinationsx, $destinationsy);
                    $src = imagecreatefrompng($logo);
                    break;
                    default:
                    throw new \Magento\Framework\Exception\LocalizedException(__("Invalid image type."));
                }
                // echo "<pre>";
                // print_r(imagesx($thumb));
                // print_r(imagesy($thumb));
                // exit();
                imagecopy($destination, $src, 0, 0, 0, 0, imagesx($thumb), imagesy($thumb));
            }
        }else{
            return;
        }
    } else if (($image != null) || ($image != '')) {
        $path = $reader . 'productdesigner/uploadwatermark/' . $image;
        if(file_exists($path)){
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($reader . 'productdesigner/uploadwatermark/' . $image);
            $imageResize->constrainOnly(FALSE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize(imagesx($destination), imagesy($destination));
            $imageResize->save($reader . 'productdesigner/uploadwatermark/resized/' . $image);
            $logo = $reader . 'productdesigner/uploadwatermark/resized/' . $image;
            if (file_exists($logo)) {
                if (getimagesize($logo)) {
                    $info = getimagesize($logo);
                    $imgtype = image_type_to_mime_type($info[2]);
                    switch ($imgtype) {
                        case 'image/jpeg':
                        $src = imagecreatefromjpeg($logo);
                        break;
                        case 'image/gif':
                        $src = imagecreatefromgif($logo);
                        break;
                        case 'image/png':
                        list($width, $height) = getimagesize($logo);
                        $newwidth = $width;
                        $newheight = $height;
                        $thumb = imagecreate($newwidth, $newheight);
                        $src = imagecreatefrompng($logo);
                        break;
                        default:
                        break;
                    }
                    list($width, $height) = getimagesize($logo);
                    imagecopy($destination, $src, 0, 0, 0, 0, imagesx($thumb), imagesy($thumb));
                }
            }else{
                return;
            }
        }
    }
    imagesavealpha($destination, true);
    switch ($imgtype) {
        case 'image/jpeg':
        imagejpeg($destination, $generatedImageFullPath, 100);
        break;
        case 'image/gif':
        imagejpeg($destination, $generatedImageFullPath, 80);
        break;
        case 'image/png':
        imagepng($destination, $generatedImageFullPath);
        break;
        default:
        break;
    }
}
}
