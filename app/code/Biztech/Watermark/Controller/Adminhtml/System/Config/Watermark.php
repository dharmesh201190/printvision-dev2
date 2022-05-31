<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Biztech\Watermark\Controller\Adminhtml\System\Config;

use Biztech\Productdesigner\Helper\Data;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Watermark extends Action {

    protected $resultJsonFactory;
    protected $_assetRepo;
    protected $_directoryList;
    protected $_filesystem;
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(
    Context $context, JsonFactory $resultJsonFactory, Data $helper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem $filesystem, \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->_assetRepo = $assetRepo;
        $this->_directoryList = $directoryList;
        $this->_filesystem = $filesystem;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        try {
            $watermarktext = $this->getRequest()->getParam('watermarktext');
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
            $prod_image_dir = $reader . 'productdesigner/uploadwatermark/';

            if (!is_dir($prod_image_dir)) {
                mkdir($prod_image_dir, 0777);
            }

            $basepath1 = $prod_image_dir . 'default';
            if (!is_dir($basepath1)) {
                mkdir($basepath1, 0777);
            }

            if ($watermarktext != '') {
                $imgWidth = \Biztech\Productdesigner\Helper\Info::ResizeWidth;
                $imgHeight = \Biztech\Productdesigner\Helper\Info::ResizeHeight;
                $im = imagecreatetruecolor($imgWidth, $imgHeight);
                $font = $this->_directoryList->getRoot() . '/app/code/Biztech/Productdesigner/view/adminhtml/web/fonts/ubuntu-regular.ttf';

                $white = imagecolorallocate($im, 255, 255, 255);
                $black = imagecolorallocate($im, 0, 0, 0);
                imagefilledrectangle($im, 0, 0, $imgWidth - 1, $imgHeight - 1, $white);
                $splitText = explode("\\n", $watermarktext);
                $lines = count($splitText);

                foreach ($splitText as $txt) {
                    $textBox = imagettfbbox(60, 30, $font, $txt);
                    $textWidth = abs(max($textBox[2], $textBox[4]));
                    $textHeight = abs(max($textBox[5], $textBox[7]));
                    // if(strlen($txt) >= 5 && strlen($txt) <= 6){
                    //     $x = ((imagesx($im) - $textWidth) / 2) + 20;
                    //     $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + 20;
                    //     $lines = $lines - 1;
                    //     imagettftext($im, 80, 42, $x, $y, $black, $font, $txt);
                    // }else if(strlen($txt) >= 7 && strlen($txt) <= 8){
                    //     $x = ((imagesx($im) - $textWidth) / 2) + 30;
                    //     $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + 30;
                    //     $lines = $lines - 1;
                    //     imagettftext($im, 55, 42, $x, $y, $black, $font, $txt);
                    // }else if(strlen($txt) >= 9 && strlen($txt) <= 10){
                    //     $x = ((imagesx($im) - $textWidth) / 2) + 50;
                    //     $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + 50;
                    //     $lines = $lines - 1;
                    //     imagettftext($im, 55, 42, $x, $y, $black, $font, $txt);
                    // }else if(strlen($txt) >= 11 && strlen($txt) <= 12){
                    //     $x = ((imagesx($im) - $textWidth) / 2) + 90;
                    //     $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + 90;
                    //     $lines = $lines - 1;
                    //     imagettftext($im, 55, 42, $x, $y, $black, $font, $txt);
                    // }else if(strlen($txt) >= 13 && strlen($txt) <= 14){
                    //     $x = ((imagesx($im) - $textWidth) / 2) + 90;
                    //     $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + 90;
                    //     $lines = $lines - 1;
                    //     imagettftext($im, 55, 42, $x, $y, $black, $font, $txt);
                    // }

                    if(strlen($txt) == 5){
                        $fontSize = 80;
                        $xCordinate = 185;
                        $yCordinate = 410;
                    }else if(strlen($txt) == 6){
                        $fontSize = 80;
                        $xCordinate = 180;
                        $yCordinate = 410;
                    }else if(strlen($txt) == 7){
                        $fontSize = 75;
                        $xCordinate = 175;
                        $yCordinate = 415;
                    }else if(strlen($txt) == 8){
                        $fontSize = 75;
                        $xCordinate = 160;
                        $yCordinate = 435;
                    }else if(strlen($txt) == 9){
                        $fontSize = 70;
                        $xCordinate = 150;
                        $yCordinate = 445;
                    }else if(strlen($txt) == 10){
                        $fontSize = 70;
                        $xCordinate = 140;
                        $yCordinate = 450;
                    }else if(strlen($txt) == 11){
                        $fontSize = 65;
                        $xCordinate = 130;
                        $yCordinate = 450;
                    }else if(strlen($txt) == 12){
                        $fontSize = 65;
                        $xCordinate = 120;
                        $yCordinate = 450;
                    }else if(strlen($txt) == 13){
                        $fontSize = 60;
                        $xCordinate = 100;
                        $yCordinate = 450;
                    }else if(strlen($txt) == 14){
                        $fontSize = 55;
                        $xCordinate = 100;
                        $yCordinate = 450;
                    }
                    // $x = ((imagesx($im) - $textWidth) / 2) + $xCordinate;
                    // $y = (((imagesy($im) + $textHeight) / 2) - ($lines - 2) * $textHeight) + $yCordinate;
                    $lines = $lines - 1;
                    imagettftext($im, $fontSize, 45, $xCordinate, $yCordinate, $black, $font, $txt);
                }

                $png = $basepath1 . '/textwatermark.png';
                imagepng($im, $png);
                chmod($png, 0777);

                $dimensions = getimagesize($png);
                $x = $dimensions[0];
                $y = $dimensions[1];
                $im = imagecreatetruecolor($x, $y);
                $src_ = imagecreatefrompng($png);
                $alpha_channel = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagecolortransparent($im, $alpha_channel);
                imagefill($im, 0, 0, $alpha_channel);
                imagecopy($im, $src_, 0, 0, 0, 0, $x, $y);
                imagesavealpha($im, true);
                imagepng($im, $png, 9);

                chmod($png, 0777);

                $image = imagecreatefrompng($png);
                $opacity = 0.25;
                imagealphablending($image, false); // imagesavealpha can only be used by doing this for some reason
                imagesavealpha($image, true); // this one helps you keep the alpha.
                $transparency = 1 - $opacity;
                imagefilter($image, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency); // the fourth parameter is alpha
                header('Content-type: image/png');
                imagepng($image, $png);
            }
            $result = $this->resultJsonFactory->create()->setData(['success' => true, 'watermarktext' => $watermarktext]);
            return $result;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $result = $this->resultJsonFactory->create()->setData(['success' => false, 'watermarktext' => $e->getMessage()]);
            return $result;
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
    }

}
