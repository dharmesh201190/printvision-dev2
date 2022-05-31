<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\Productdesigner\Controller\Adminhtml\Productdesigner;

use \Spipu\Html2Pdf\Html2Pdf;

class downloadItemOrder extends \Magento\Backend\App\Action {

    protected $_order;
    protected $_fileSystem;
    protected $designOrderFactory;
    protected $_mediaGallery;
    protected $_side;
    protected $_designimagesFactory;
    protected $fontCollectionFactory;
    protected $_infoHelper;
    protected $_orderHelper;
    protected $_storeManager;
    protected $_helper;
    protected $_designOrdersFactory;
    protected $_productLoader;
    protected $_designsFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Filesystem $fileSystem,
        \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrderFactory,
        \Biztech\Productdesigner\Model\ResourceModel\MediaGallery\CollectionFactory $mediaGallery,
        \Biztech\Productdesigner\Model\Side $side,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designimagesFactory,
        \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrdersFactory,
        \Biztech\Productdesigner\Helper\Info $infoHelper,
        \Biztech\Productdesigner\Helper\Order $orderHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Biztech\Productdesigner\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $_productLoade,
        \Biztech\Productdesigner\Model\DesignsFactory $designsFactory
    ) {
        parent::__construct($context);
        $this->_order = $order;
        $this->_fileSystem = $fileSystem;
        $this->designOrderFactory = $designOrderFactory;
        $this->_mediaGallery = $mediaGallery;
        $this->_side = $side;
        $this->_designimagesFactory = $designimagesFactory;
        $this->_infoHelper = $infoHelper;
        $this->_orderHelper = $orderHelper;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_designOrdersFactory = $designOrdersFactory;  
        $this->_productLoader = $_productLoade;
        $this->_designsFactory = $designsFactory;
    }

    public function execute() {
        $params = $this->getRequest()->getParams();
        $order_id = $params['order_id'];
        $item_id = !empty($params['item_id']) ? $params['item_id'] : 0;;
        $designs_id = !empty($params['design_id']) ? $params['design_id'] : 0;
        $apiOrder = !empty($params['apiOrder']) ? $params['apiOrder'] : false;
        $storeid = $this->_storeManager->getStore()->getId();
        $checkIsIntegration = $this->_helper->getConfig("integration/general/code", $storeid);
        
        if(($checkIsIntegration && !empty($designs_id)) || $apiOrder == true){
            $designIds = array();
            if($apiOrder == true){
                $orders = $this->_designOrdersFactory->create()->addFieldToFilter("order_id",$order_id)->getData();
                foreach ($orders as $orderkey => $ordervalue) {
                    if(!empty($ordervalue['design_id'])){
                       array_push($designIds, $ordervalue['design_id']);
                    }
                }                
            }else{
                 array_push($designIds, $designs_id);
            }


            foreach ($designIds as $designKey => $designValue) {
                $designs_id = $designValue;
                $order = $this->_designOrdersFactory->create()->addFieldToFilter("design_id",$designs_id)->getFirstItem()->getData();
                $order_id = $order['order_id'];
                $order_increment_id = $order_id;
                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $order_dir = $reader->getAbsolutePath() . 'productdesigner/order';
                if (!file_exists($order_dir)) {
                    mkdir($order_dir, 0777, true);
                }
                $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id;
                if (!file_exists($zip_dir)) {
                    mkdir($zip_dir, 0777, true);
                }

                    $itemID = $designs_id;
                    $designData = $this->_designsFactory->create()->load($designs_id)->getData();
                    $product = $this->_productLoader->create()->load($designData['associated_product_id']);
                    $sku = $product->getSku();
                    $item_path = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $itemID;
                    $zip_path = array();

                            if (!file_exists($item_path)) {
                                mkdir($item_path, 0777, true);
                            }
                                $content = '';
                                $design_id = $designs_id;
                                $this->_orderHelper->getOriginalImages($item_path, $design_id);
                                $designImages = $this->_designimagesFactory->create()->addFieldToFilter('design_id', array('eq' => $design_id));
                          /*      foreach ($designImages as $designImage) {
                                    if ($designImage->getTemplateMediaId()) {
                                        $side = $this->_infoHelper->getImageSideFromTemplateMedia($designImage->getTemplateMediaId(), true, $this->_mediaGallery, $this->_side);
                                    } else {
                                        $side = $this->_infoHelper->getImageSideFromTemplateMedia($designImage->getProductImageId(), false, $this->_mediaGallery, $this->_side);
                                    }
                                    $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                                    if ($designImage->getDesignImageType() == 'base_high') {
                                        $name = $designImage->getImagePath();
                                        $pdfname = 'Base -' . $sku . '-' . $side;
                                        $designImagesBaseDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/base/';
                                        $baseArr[] = scandir($designImagesBaseDir, 1);
                                        foreach ($baseArr as $files) {
                                            foreach ($files as $key => $value) {
                                                if ('/' . $value == $name) {
                                                    $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                    $pngName = $imagePreFix . '.png';
                                                    $jpgName = $imagePreFix . '.jpg';
                                                    $name1 = 'Base-' . $sku . '-' . $side;
                                                    copy($designImagesBaseDir . $pngName, $item_path . '/' . $pngName);
                                                    rename($item_path . '/' . $pngName, $item_path . '/' . $name1 . '.png');
                                                    copy($designImagesBaseDir . $jpgName, $item_path . '/' . $jpgName);
                                                    rename($item_path . '/' . $jpgName, $item_path . '/' . $name1 . '.jpg');
                                                }
                                            }
                                        }
                                        $this->_orderHelper->generatePDF($designImagesBaseDir, $name, $design_id, $side, $item_path, $order_increment_id, $pdfname);
                                    }

                                    if ($designImage->getDesignImageType() == 'orig_high') {
                                        $name = $designImage->getImagePath();
                                        $pdfname = 'Print -' . $sku . '-' . $side;
                                        $designImagesOrigDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/orig/';
                                        $origArr[] = scandir($designImagesOrigDir, 1);
                                        foreach ($origArr as $files) {
                                            foreach ($files as $key => $value) {
                                                if ('/' . $value == $name) {
                                                    $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                    $pngName = $imagePreFix . '.png';
                                                    $jpgName = $imagePreFix . '.jpg';
                                                    $name1 = 'Print-' . $sku . '-' . $side;
                                                    copy($designImagesOrigDir . $pngName, $item_path . '/' . $pngName);
                                                    rename($item_path . '/' . $pngName, $item_path . '/' . $name1 . '.png');
                                                    copy($designImagesOrigDir . $jpgName, $item_path . '/' . $jpgName);
                                                    rename($item_path . '/' . $jpgName, $item_path . '/' . $name1 . '.jpg');
                                                }
                                            }
                                        }
                                        $this->_orderHelper->generatePDF($designImagesOrigDir, $name, $design_id, $side, $item_path, $order_increment_id, $pdfname);
                                    }
                                    if($designImage->getDesignImageType() == 'svg'){
                                        $svgNname = $designImage->getImagePath();
                                        $designImagesSVGDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/svg/';
                                        $svgArr[] = scandir($designImagesSVGDir, 1);
                                        foreach ($svgArr as $files) {
                                            foreach ($files as $key => $value) {
                                                if ('/' . $value == $svgNname) {
                                                    $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                    $svgOldName = $imagePreFix . '.svg';
                                                    $svgname1 = 'Svg-' . $sku . '-' . $side;
                                                    copy($designImagesSVGDir . $svgOldName, $item_path . '/' . $svgOldName);
                                                    rename($item_path . '/' . $svgOldName, $item_path . '/' . $svgname1 . '.svg');
                                                }
                                            }
                                        }
                                    }
                                }
                        $this->_orderHelper->generateApiCSV($item_path, $order, $order_id, $designs_id, 'true');*/
                        $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $itemID;
                        $zipName = $order_increment_id . '_' . $itemID . '_' . addslashes($sku);
                        $this->_orderHelper->createEntireFolderZip($item_path, $zipName, $zip_dir);
            }

        }else{
            $order_increment_id = $this->_order->load($order_id)->getIncrementId();
            $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $order_dir = $reader->getAbsolutePath() . 'productdesigner/order';
            if (!file_exists($order_dir)) {
                mkdir($order_dir, 0777, true);
            }
            $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id;
            $order = $this->_order->load($order_id);
            if (!file_exists($zip_dir)) {
                mkdir($zip_dir, 0777, true);
            }

            foreach ($order->getAllVisibleItems() as $item) {
            
                $itemID = $item->getQuoteItemId();
                $sku = $item->getSku();
                $design_id = ($item->getProductOptionByCode('additional_options')[0]['design_id']);
                $item_path = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $design_id;
                $zip_path = array();                

                if($item->getId() == $item_id){

                    /*if ($item->getProductOptionByCode('additional_options')) {
                        if (!file_exists($item_path)) {
                            mkdir($item_path, 0777, true);
                        }
                        if (isset($item->getProductOptionByCode('additional_options')[0]['design_id'])) {
                            $content = '';
                            $design_id = ($item->getProductOptionByCode('additional_options')[0]['design_id']);
                            $this->_orderHelper->getOriginalImages($item_path, $design_id);
                            $designImages = $this->_designimagesFactory->create()->addFieldToFilter('design_id', array('eq' => $design_id));
                            foreach ($designImages as $designImage) {
                                if ($designImage->getTemplateMediaId()) {
                                    $side = $this->_infoHelper->getImageSideFromTemplateMedia($designImage->getTemplateMediaId(), true, $this->_mediaGallery, $this->_side);
                                } else {
                                    $side = $this->_infoHelper->getImageSideFromTemplateMedia($designImage->getProductImageId(), false, $this->_mediaGallery, $this->_side);
                                }
                                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                                if ($designImage->getDesignImageType() == 'base_high') {
                                    $name = $designImage->getImagePath();
                                    $pdfname = 'Base -' . $sku . '-' . $side;
                                    $designImagesBaseDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/base/';
                                    $baseArr[] = scandir($designImagesBaseDir, 1);
                                    foreach ($baseArr as $files) {
                                        foreach ($files as $key => $value) {
                                            if ('/' . $value == $name) {
                                                $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                $pngName = $imagePreFix . '.png';
                                                $jpgName = $imagePreFix . '.jpg';
                                                $name1 = 'Base-' . $sku . '-' . $side;
                                                copy($designImagesBaseDir . $pngName, $item_path . '/' . $pngName);
                                                rename($item_path . '/' . $pngName, $item_path . '/' . $name1 . '.png');
                                                copy($designImagesBaseDir . $jpgName, $item_path . '/' . $jpgName);
                                                rename($item_path . '/' . $jpgName, $item_path . '/' . $name1 . '.jpg');
                                            }
                                        }
                                    }
                                    $this->_orderHelper->generatePDF($designImagesBaseDir, $name, $design_id, $side, $item_path, $order_increment_id, $pdfname);
                                }

                                if ($designImage->getDesignImageType() == 'orig_high') {
                                    $name = $designImage->getImagePath();
                                    $pdfname = 'Print -' . $sku . '-' . $side;
                                    $designImagesOrigDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/orig/';
                                    $origArr[] = scandir($designImagesOrigDir, 1);
                                    foreach ($origArr as $files) {
                                        foreach ($files as $key => $value) {
                                            if ('/' . $value == $name) {
                                                $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                $pngName = $imagePreFix . '.png';
                                                $jpgName = $imagePreFix . '.jpg';
                                                $name1 = 'Print-' . $sku . '-' . $side;
                                                copy($designImagesOrigDir . $pngName, $item_path . '/' . $pngName);
                                                rename($item_path . '/' . $pngName, $item_path . '/' . $name1 . '.png');
                                                copy($designImagesOrigDir . $jpgName, $item_path . '/' . $jpgName);
                                                rename($item_path . '/' . $jpgName, $item_path . '/' . $name1 . '.jpg');
                                            }
                                        }
                                    }
                                    $this->_orderHelper->generatePDF($designImagesOrigDir, $name, $design_id, $side, $item_path, $order_increment_id, $pdfname);
                                }

                                if($designImage->getDesignImageType() == 'svg'){
                                    $svgNname = $designImage->getImagePath();
                                    $designImagesSVGDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/svg/';
                                    $svgArr[] = scandir($designImagesSVGDir, 1);
                                    foreach ($svgArr as $files) {
                                        foreach ($files as $key => $value) {
                                            if ('/' . $value == $svgNname) {
                                                $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                                $svgOldName = $imagePreFix . '.svg';
                                                $svgname1 = 'Svg-' . $sku . '-' . $side;
                                                copy($designImagesSVGDir . $svgOldName, $item_path . '/' . $svgOldName);
                                                rename($item_path . '/' . $svgOldName, $item_path . '/' . $svgname1 . '.svg');
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }*/
                    $this->_orderHelper->generateCSV($item_path, $order, $order_id, $item_id, 'true');
                    $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $design_id;
                    $zipName = $order_increment_id . '_' . $design_id . '_' . addslashes($item->getSku());
                    $this->_orderHelper->createEntireFolderZip($item_path, $zipName, $zip_dir);
                }
            }   
            
        }

    }
}
