<?php
namespace Biztech\NameNumber\Controller\Index;

use \Spipu\Html2Pdf\Html2Pdf;

class generateNameNumberImage extends \Magento\Framework\App\Action\Action {

    // Variable Declaration
    protected $_storeManager;
    protected $infoHelper;
    protected $design;
    protected $selectionareaCollection;
    protected $dir;
    protected $pdHelper;
    protected $designImagesFactory;
    protected $designOrderCollection;
    protected $_fileSystem;
    protected $_order;
    protected $_mediaGallery;
    protected $_designimagesFactory;
    protected $_side;
    protected $_designFactory;
    protected $_eventManager;
    protected $_objectFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Biztech\Productdesigner\Helper\Info $infoHelper, 
        \Biztech\Productdesigner\Helper\Data $pdHelper, 
        \Biztech\Productdesigner\Model\DesignsFactory $design, 
        \Biztech\Productdesigner\Model\Mysql4\Selectionarea\CollectionFactory $selectionareaCollection, 
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Biztech\Productdesigner\Model\DesignimagesFactory $designImagesFactory,
        \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrderCollection,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Sales\Model\Order $order,
        \Biztech\Productdesigner\Model\ResourceModel\MediaGallery\CollectionFactory $mediaGallery,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designimagesFactory,
        \Biztech\Productdesigner\Model\Side $side,
        \Biztech\Productdesigner\Model\Mysql4\Designs\CollectionFactory $designFactory,
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->infoHelper = $infoHelper;
        $this->pdHelper = $pdHelper;
        $this->design = $design;
        $this->selectionareaCollection = $selectionareaCollection;
        $this->dir = $dir;
        $this->designImagesFactory = $designImagesFactory;
        $this->designOrderCollection = $designOrderCollection;
        $this->_fileSystem = $fileSystem;
        $this->_order = $order;
        $this->_mediaGallery = $mediaGallery;
        $this->_designimagesFactory = $designimagesFactory;
        $this->_side = $side;
        $this->_designFactory  =$designFactory;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        parent::__construct($context);
    }

    
    public function execute() {
        try {
            $imageGeneratedName = '';
            $imageSideName = '';
            $destinationPDFPath = '';
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = $data['productId'];
            $designId = $data['designId'];
            $orderId = $data['orderId'];
            $nameNumberIndex = $data['nameNumberIndex'];
            $orderPrintedName = '';
            $orderPrintedNumber = '';
            if(isset($data['name']) && $data['name'] != ''){
                $orderPrintedName = $data['name'];
            }
            if(isset($data['number']) && $data['number'] != ''){
                $orderPrintedNumber = $data['number'];
            }
            $base64DataFile= $data['base64DataFile'];
            $sidesAndParentImageIDs= $data['sidesAndParentImageIDs'];
            // $canvasSVG = isset($data['canvasSVG']) ? json_decode(base64_decode($data['canvasSVG'])) : '';
            $dir = $this->dir->getPath('media')  . '/productdesigner/canvasData/';
            $filename = $dir . $base64DataFile.'.txt';
            $filesize = 0;
            if (file_exists($filename)) {
                $filesize = filesize($filename);
            }

            $canvasData = '';
            if ($filesize > 0) {
                $readMyfile = fopen($filename, "r");
                $canvasData = fread($readMyfile, filesize($filename));
                fclose($readMyfile);
            }
            $canvasDataURL = json_decode(base64_decode($canvasData), true);
            $merged_large_images = array();
            foreach ($canvasDataURL as $key => $large_image) {
                $newkey = str_replace('@', '', $key);
                $newkey1 = strstr($newkey, "&", true);
                $newkey2 = str_replace('&', '', strstr($newkey, "&", false));
                $merged_large_images[$newkey1][$newkey2] = $large_image;
            }
            $productData = $this->infoHelper->getProductTypeAndMediaImages($productId);
            $grouped_product_images = $productData['media_image'];
            $product_type = $productData['product_type'];
            $design = $this->design->create()->load($designId);
            $associatedProductId = $design->getAssociatedProductId();
            $selectionareas = $this->selectionareaCollection->create()->addFieldToFilter('product_id', array('in' => array($associatedProductId, $productId)))->getData();
            $dimensions = array();
            foreach ($selectionareas as $selectionarea) {
                $dimensions[$selectionarea['image_id']][$selectionarea['design_area_id']] = $selectionarea;
            }
            
            $params = array();
            $params['designImagesBaseDir'] = $this->dir->getPath('media') . '/productdesigner/designs/' . $designId . '/base/';
            $params['designImagesOrigDir'] = $this->dir->getPath('media') . '/productdesigner/designs/' . $designId . '/orig/';
            if (!file_exists($params['designImagesBaseDir'])) {
                mkdir($params['designImagesBaseDir'], 0777, true);
            }
            if (!file_exists($params['designImagesOrigDir'])) {
                mkdir($params['designImagesOrigDir'], 0777, true);
            }
            $params['design_id'] = $designId;
            $params['productId'] = $productId;
            $params['canvasRatio'] = \Biztech\Productdesigner\Helper\Info::CanvasRatio;
            if ($product_type == 'configurable') {
                $parentImageIds = $sidesAndParentImageIDs[1];
            }
            foreach ($merged_large_images as $productImageId => $large_image) {
              
                   $prod_image_path = $grouped_product_images[$productImageId]['path'];
                 if (!isset($dimensions[$productImageId])) {
                    $productImageId = $parentImageIds[$productImageId];
                }
             
                $params['prod_image_path'] = $prod_image_path;
                $params['dimensions'] = $dimensions[$productImageId];
                $params['large_image'] = $large_image;
                $all_design_images[] = $this->generateDesignImages($params);
                
                $data['image_id'][] = $productImageId;
            }
            // $this->generateSVGImages($canvasSVG, $designId);

            //Save Data in productdesigner_design_images table
            foreach ($all_design_images as $key => $value) {
                $image_id = $data['image_id'][$key];
                if(isset($value['base_high'])){
                    $designImagesModel = $this->designImagesFactory->create();
                    $designImagesModel->setDesignId($designId)
                            ->setDesignImageType('basenamenumber')
                            ->setProductImageId($image_id)
                            ->setImagePath(str_replace('\\', '/', $value['base_high']));
                    $designImagesModel->save();
                }
                if(isset($value['orig_high'])){
                    $designImagesModel = $this->designImagesFactory->create();
                    $designImagesModel->setDesignId($designId)
                            ->setDesignImageType('orignamenumber')
                            ->setProductImageId($image_id)
                            ->setImagePath(str_replace('\\', '/', $value['orig_high']));
                    $designImagesModel->save();
                }
            }

            $storeid = $this->_storeManager->getStore()->getId();
            $checkIsIntegration = $this->pdHelper->getConfig("integration/general/code", $storeid);
            /*echo $orderId;exit();*/
            if($checkIsIntegration){
                $order_increment_id = $this->_order->load($orderId)->getIncrementId();
                $orders = $this->designOrderCollection->create()->addFieldToFilter("order_id",$order_increment_id)->getData();
                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $order_dir = $reader->getAbsolutePath() . 'productdesigner/order';
                if (!file_exists($order_dir)) {
                    mkdir($order_dir, 0777, true);
                }
                $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id;
                if (!file_exists($zip_dir)) {
                    mkdir($zip_dir, 0777, true);
                }
                foreach ($orders as $item) {
                    if($designId != $item['design_id'])
                        continue;
                    $designData = $this->design->create()->load($item['design_id'])->getData();
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                    $product = $objectManager->create('\Magento\Catalog\Model\Product')->load($designData['associated_product_id']);

                    $itemID = !empty($item['design_id']) ? $item['design_id'] : "";
                    $sku = $product->getSku();
                    $sku = str_replace("-"," ",$sku);
                    $item_path = $reader->getAbsolutePath() . 'productdesigner/order/'. $order_increment_id . '/' . $itemID;
                    $zip_path = array();
                    $design_id = !empty($item['design_id']) ? $item['design_id'] : "";

                    if (!file_exists($item_path)) {
                       mkdir($item_path, 0777, true);
                    }
                    if(!file_exists($item_path . '/namenumber')){
                       mkdir($item_path . '/namenumber', 0777, true);
                    }
                    $this->getOriginalImages($item_path, $itemID, $nameNumberIndex);
                    $designImages = $this->_designimagesFactory->create()->addFieldToFilter('design_id', array('eq' => $itemID));
                                        
                    foreach ($designImages as $designImage) {

                        $mediaGallery = $this->_mediaGallery->create()->addFieldToFilter('value_id', $designImage->getProductImageId());
                        $mediaGalleryData = ($mediaGallery->getData()[0]);
                        $imageSide = $mediaGalleryData['image_side'];
                        $sidecollection = $this->_side->load($imageSide);
                        $side = $sidecollection->getImagesideTitle();
                        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        
                        if ($designImage->getDesignImageType() == 'basenamenumber'){
                            $name = $designImage->getImagePath();
                            $designImagesBaseDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/base/';
                            $baseArr[]=scandir($designImagesBaseDir,1);
                            $name1 = '';
                            if((isset($orderPrintedName) && $orderPrintedName != '') && (isset($orderPrintedNumber) && $orderPrintedNumber != '')){
                                $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedName . '-' .$orderPrintedNumber;
                            }else if(isset($orderPrintedName) && $orderPrintedName != ''){
                                $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedName;
                            }else if(isset($orderPrintedNumber) && $orderPrintedNumber != ''){
                                $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedNumber;
                            }
                            foreach($baseArr as $files){
                                foreach ($files as $key => $value) {
                                    if ('/'.$value == $name){
                                        $imagePreFix = ltrim(strstr($value, '.', true),"'\'");
                                        $pngName= $imagePreFix .'.png';
                                        $jpgName = $imagePreFix . '.jpg';
                                        copy($designImagesBaseDir.$pngName,$item_path.'/namenumber/'.$pngName);
                                        rename($item_path. '/namenumber/'.$pngName, $item_path.'/namenumber/'.$name1.'.png');
                                        copy($designImagesBaseDir.$jpgName,$item_path.'/namenumber/'.$jpgName);
                                        rename($item_path. '/namenumber/'.$jpgName, $item_path.'/namenumber/'.$name1.'.jpg');
                                    }
                                }
                            }
                            foreach ($all_design_images as $images) {
                                foreach ($images as $key => $value) {
                                    if ($key == 'base_high') {
                                        $this->generatePDF($params['designImagesBaseDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                    } else {
                                        $this->generatePDF($params['designImagesOrigDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                    }
                                }
                            }
                            $this->downloadAllImagesPdf($all_design_images, $designId, $orderId, $name1, $item_path. '/namenumber/');
                        }
                        if ($designImage->getDesignImageType() == 'orignamenumber'){
                            $name = $designImage->getImagePath();
                            $designImagesOrigDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/orig/';
                            $origArr[]=scandir($designImagesOrigDir,1);
                            $name1 = '';
                            if((isset($orderPrintedName) && $orderPrintedName != '') && (isset($orderPrintedNumber) && $orderPrintedNumber != '')){
                                $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedName . '-' .$orderPrintedNumber;
                            }else if(isset($orderPrintedName) && $orderPrintedName != ''){
                                $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedName;
                            }else if(isset($orderPrintedNumber) && $orderPrintedNumber != ''){
                                $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedNumber;
                            }
                            foreach($origArr as $files){
                                foreach ($files as $key => $value) {
                                    if ('/'.$value == $name){
                                        $imagePreFix = ltrim(strstr($value, '.', true),"'\'");
                                        $pngName= $imagePreFix .'.png';
                                        $jpgName = $imagePreFix . '.jpg';
                                        copy($designImagesOrigDir.$pngName,$item_path.'/namenumber/'.$pngName);
                                        rename($item_path. '/namenumber/'.$pngName, $item_path.'/namenumber/'.$name1.'.png');
                                        copy($designImagesOrigDir . $jpgName, $item_path . '/namenumber/' . $jpgName);
                                        rename($item_path. '/namenumber/'.$jpgName, $item_path.'/namenumber/'.$name1.'.jpg');
                                    }
                                }
                            }
                            foreach ($all_design_images as $images) {
                                foreach ($images as $key => $value) {
                                    if ($key == 'base_high') {
                                        $this->generatePDF($params['designImagesBaseDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                    } else {
                                        $this->generatePDF($params['designImagesOrigDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                    }
                                }
                            }
                            $this->downloadAllImagesPdf($all_design_images, $designId, $orderId, $name1, $item_path. '/namenumber/');
                        }

                        //Make generated svg zip and copy
                        if ($designImage->getDesignImageType() == 'svg') {
                            $svgNname = $designImage->getImagePath();
                            $svg_path = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/' . 'svg/';
                            $files = scandir($svg_path);
                            foreach($files as $key=>$value){
                                $iscmyk = explode("_", $value);
                                $iscmyk = $iscmyk[count($iscmyk)-1];
                                if($value!='.' && $value!='..' && $iscmyk != 'cmyk.svg' && $value != $svgNname){
                                    $zip_path[] = $svg_path . $value;
                                }
                            }
                            $name = 'Print-'.$sku;
                            $this->createSvgZip($zip_path, $item_path . '/namenumber/', $name);
                        }
                    }
                }

                //PRODUCTDESIGNER_ORDER TABLE SET FLAG 0
                $designOrderCollectionModel = $this->designOrderCollection->create()->addFieldToFilter('design_id', ['eq' => $designId]);
                foreach ($designOrderCollectionModel as $value) {
                    if($value->getFlag() == 1){
                        $value->setFlag(0);
                    }
                }
                $designOrderCollectionModel->save();
                $this->getResponse()->setBody(json_encode($all_design_images));

            }else{

                //FOR DOWNLOAD ALL ORDER
                $order = $this->_order->load($orderId);
                $order_increment_id = $this->_order->load($orderId)->getIncrementId();
                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $order_dir = $reader->getAbsolutePath() . 'productdesigner/order';
                if (!file_exists($order_dir)) {
                    mkdir($order_dir, 0777, true);
                }
                $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/'. $order_increment_id;
                if (!file_exists($zip_dir)) {
                    mkdir($zip_dir, 0777, true);
                }
                foreach ($order->getAllVisibleItems() as $item) {
                    $itemID = $item->getQuoteItemId();
                    $sku = $item->getSku();
                    $sku = str_replace("-"," ",$sku);
                    $design_id = "";
                    if(!empty($item->getProductOptionByCode('additional_options'))){
                        $design_id = ($item->getProductOptionByCode('additional_options')[0]['design_id']);
                    }
                    $itemID = !empty($design_id) ? $design_id : $itemID;
                    $item_path = $reader->getAbsolutePath() . 'productdesigner/order/'. $order_increment_id . '/' . $itemID;
                    $zip_path = array();

                    if($item->getProductOptionByCode('additional_options')){
                        if (!file_exists($item_path)) {
                            mkdir($item_path, 0777, true);
                        }
                        if(!file_exists($item_path . '/namenumber')){
                            mkdir($item_path . '/namenumber', 0777, true);
                        }
                          
                        if(isset(($item->getProductOptionByCode('additional_options')[0]['design_id']))){
                            $content = '';
                            $design_id = ($item->getProductOptionByCode('additional_options')[0]['design_id']);
                             if($designId != $design_id)
                                     continue;
                            $this->getOriginalImages($item_path, $design_id, $nameNumberIndex);
                            $designImages = $this->_designimagesFactory->create()->addFieldToFilter('design_id', array('eq' => $design_id));
                            foreach ($designImages as $designImage) {
                                $mediaGallery = $this->_mediaGallery->create()->addFieldToFilter('value_id', $designImage->getProductImageId());
                                $mediaGalleryData = ($mediaGallery->getData()[0]);
                                $imageSide = $mediaGalleryData['image_side'];
                                $sidecollection = $this->_side->load($imageSide);
                                $side = $sidecollection->getImagesideTitle();
                                $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                                
                                if ($designImage->getDesignImageType() == 'basenamenumber'){
                                    $name = $designImage->getImagePath();
                                    $designImagesBaseDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/base/';
                                    $baseArr[]=scandir($designImagesBaseDir,1);
                                    $name1 = '';
                                    if((isset($orderPrintedName) && $orderPrintedName != '') && (isset($orderPrintedNumber) && $orderPrintedNumber != '')){
                                        $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedName . '-' .$orderPrintedNumber;
                                    }else if(isset($orderPrintedName) && $orderPrintedName != ''){
                                        $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedName;
                                    }else if(isset($orderPrintedNumber) && $orderPrintedNumber != ''){
                                        $name1 = 'Base-'.$sku.'-'. $side . '-' . $orderPrintedNumber;
                                    }
                                    foreach($baseArr as $files){
                                        foreach ($files as $key => $value) {
                                            if ('/'.$value == $name){
                                                $imagePreFix = ltrim(strstr($value, '.', true),"'\'");
                                                $pngName= $imagePreFix .'.png';
                                                $jpgName = $imagePreFix . '.jpg';
                                                copy($designImagesBaseDir.$pngName,$item_path.'/namenumber/'.$pngName);
                                                rename($item_path. '/namenumber/'.$pngName, $item_path.'/namenumber/'.$name1.'.png');
                                                copy($designImagesBaseDir.$jpgName,$item_path.'/namenumber/'.$jpgName);
                                                rename($item_path. '/namenumber/'.$jpgName, $item_path.'/namenumber/'.$name1.'.jpg');
                                            }
                                        }
                                    }
                                    foreach ($all_design_images as $images) {
                                        foreach ($images as $key => $value) {
                                            if ($key == 'base_high') {
                                                $this->generatePDF($params['designImagesBaseDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                            } else {
                                                $this->generatePDF($params['designImagesOrigDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                            }
                                        }
                                    }
                                    $this->downloadAllImagesPdf($all_design_images, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                }
                                if ($designImage->getDesignImageType() == 'orignamenumber'){
                                    $name = $designImage->getImagePath();
                                    $designImagesOrigDir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/orig/';
                                    $origArr[]=scandir($designImagesOrigDir,1);
                                    $name1 = '';
                                    if((isset($orderPrintedName) && $orderPrintedName != '') && (isset($orderPrintedNumber) && $orderPrintedNumber != '')){
                                        $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedName . '-' .$orderPrintedNumber;
                                    }else if(isset($orderPrintedName) && $orderPrintedName != ''){
                                        $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedName;
                                    }else if(isset($orderPrintedNumber) && $orderPrintedNumber != ''){
                                        $name1 = 'Print-'.$sku.'-'. $side . '-' . $orderPrintedNumber;
                                    }
                                    foreach($origArr as $files){
                                        foreach ($files as $key => $value) {
                                            if ('/'.$value == $name){
                                                $imagePreFix = ltrim(strstr($value, '.', true),"'\'");
                                                $pngName= $imagePreFix .'.png';
                                                $jpgName = $imagePreFix . '.jpg';
                                                copy($designImagesOrigDir.$pngName,$item_path.'/namenumber/'.$pngName);
                                                rename($item_path. '/namenumber/'.$pngName, $item_path.'/namenumber/'.$name1.'.png');
                                                copy($designImagesOrigDir . $jpgName, $item_path . '/namenumber/' . $jpgName);
                                                rename($item_path. '/namenumber/'.$jpgName, $item_path.'/namenumber/'.$name1.'.jpg');
                                            }
                                        }
                                    }
                                    foreach ($all_design_images as $images) {
                                        foreach ($images as $key => $value) {
                                            if ($key == 'base_high') {
                                                $this->generatePDF($params['designImagesBaseDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                            } else {
                                                $this->generatePDF($params['designImagesOrigDir'], $value, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                            }
                                        }
                                    }
                                    $this->downloadAllImagesPdf($all_design_images, $designId, $orderId, $name1, $item_path. '/namenumber/');
                                }

                                //Make generated svg zip and copy
                                if ($designImage->getDesignImageType() == 'svg') {
                                    $svgNname = $designImage->getImagePath();
                                    $svg_path = $reader->getAbsolutePath() . 'productdesigner/designs/' . $design_id . '/' . 'svg/';
                                    $files = scandir($svg_path);
                                    foreach($files as $key=>$value){
                                        $iscmyk = explode("_", $value);
                                        $iscmyk = $iscmyk[count($iscmyk)-1];
                                        if($value!='.' && $value!='..' && $iscmyk != 'cmyk.svg' && $value != $svgNname){
                                            $zip_path[] = $svg_path . $value;
                                        }
                                    }
                                    $name = 'Print-'.$sku;
                                    $this->createSvgZip($zip_path, $item_path . '/namenumber/', $name);
                                }
                            }
                        }
                    }
                }
                //FOR DOWNLOAD ALL ORDER

                //PRODUCTDESIGNER_ORDER TABLE SET FLAG 0
                $designOrderCollectionModel = $this->designOrderCollection->create()->addFieldToFilter('design_id', ['eq' => $designId]);
                foreach ($designOrderCollectionModel as $value) {
                    if($value->getFlag() == 1){
                        $value->setFlag(0);
                    }
                }
                $designOrderCollectionModel->save();
                $this->getResponse()->setBody(json_encode($all_design_images));
            }
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Productdesigner.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $text = "getCartData: " . $e->getMessage();
            $logger->info($text);
            $this->getResponse()->setBody($e->getMessage());
        }
    }

    private function generateDesignImages($params) {
        /**
         * It will format large image data
         */
        $source = $this->processLargeImages($params['large_image']);
        /**
         * Initialize variable from params
         */
        $designImagesBaseDir = $params['designImagesBaseDir'];
        $designImagesOrigDir = $params['designImagesOrigDir'];
        $allDimensions = $params['dimensions'];
        $productId = $params['productId'];
        
        $canvasRatio = $params['canvasRatio'];
        $prod_image_path = $params['prod_image_path'];
        list($resize_width, $resize_height) = $this->infoHelper->calculateResizeWidthHeight($prod_image_path);

        /** Start Added By AJ. Custom Size Output * */
        $customObject = $this->_objectFactory->create();
        $customObject->setImage(array('imagepath' => $prod_image_path, 'product_id' => $productId, 'base_path' => $designImagesBaseDir, 'source' => $source, 'allDimensions' => $allDimensions));
        $this->_eventManager->dispatch('generated_images_custom_size', ['imagedata' => $customObject]);
        $prod_image_path = $customObject->getImage('imagepath') ?: $params['prod_image_path'];
        $source = $customObject->getImage('source');
        /** End Added By AJ. Custom Size Output * */

        list($iWidth, $iHeight, $imageType) = getimagesize($prod_image_path);
        /**
         * Check for canvas ratio
         */
        $enable_handles = (count($allDimensions) > 1) ? false : $this->infoHelper->IsEnableHandles($productId);
        /**
         * It will create source image with product image
         */
        list($base_image_name, $destination) = $this->infoHelper->createSourceImage($prod_image_path);

        /**
         * It will create source image with product image
         */
        list($origDestination, $orig_image_name) = $this->createBlankImage($iWidth, $iHeight);

        /**
         * Dimension loop to generate image with canvas ratio calculation
         */
        foreach ($allDimensions as $dimension) {
            $selection_area = json_decode($dimension['selection_area'], true);
            if ($enable_handles) {
                $clipx = $clipy = $canvasRatio;
                if ($selection_area['height'] + $canvasRatio > $resize_height) {
                    $clipy = $resize_height / $selection_area['height'];
                }
                if ($selection_area['width'] + $canvasRatio > $resize_width) {
                    $clipx = $resize_width / $selection_area['width'];
                }
            } else {
                $clipx = $clipy = 0;
            }
            $x1 = $selection_area['x1'] - (($selection_area['width'] + $clipx - $selection_area['width']) / 2);
            $y1 = $selection_area['y1'] - (($selection_area['height'] + $clipy - $selection_area['height']) / 2);
            $imageIdDesignAreaId = '@' . $dimension['image_id'] . '&' . $dimension['design_area_id'];
            $imageIdDesignAreaId = $dimension['design_area_id'];
            $newX1 = ($iWidth * $x1) / $resize_width;
            $newY1 = ($iHeight * $y1) / $resize_height;
            /**
             * Canvas data with product image
             */
            if (isset($source[$imageIdDesignAreaId])) {
                imagecopy($destination, $source[$imageIdDesignAreaId], $newX1, $newY1, 0, 0, imagesx($source[$imageIdDesignAreaId]), imagesy($source[$imageIdDesignAreaId]));
            }
            /**
             * Canvas data with blank image
             */
            if (isset($source[$imageIdDesignAreaId])) {
                imagecopy($origDestination, $source[$imageIdDesignAreaId], $newX1, $newY1, 0, 0, imagesx($source[$imageIdDesignAreaId]), imagesy($source[$imageIdDesignAreaId]));
            }
        }
        $imageType = image_type_to_mime_type($imageType);
        imagesavealpha($destination, true);
        switch ($imageType) {
            case 'image/jpeg':
                imagejpeg($destination, $designImagesBaseDir . $base_image_name, 100);
                /**
                 * It will convert image to PNG
                 */
                $this->pdHelper->convertImage('png', $designImagesBaseDir, $base_image_name);
                break;
            case 'image/png':
                imagepng($destination, $designImagesBaseDir . $base_image_name);
                /**
                 * It will convert image to JPG
                 */
                $this->pdHelper->convertImage('jpg', $designImagesBaseDir, $base_image_name);
                break;
        }
        imagedestroy($destination);
        imagesavealpha($origDestination, true);
        imagepng($origDestination, $designImagesOrigDir . $orig_image_name);
        imagedestroy($origDestination);
        /**
         * It will convert DES image to JPG
         */
        $this->pdHelper->convertImage('jpg', $designImagesOrigDir, $orig_image_name);
        $result = array("base_high" => '/' . $base_image_name, "orig_high" => '/' . $orig_image_name);
        return $result;
    }

    private function createBlankImage($iWidth, $iHeight) {
        $destination = imagecreatetruecolor($iWidth, $iHeight);
        imagesavealpha($destination, true);
        $imageColor = imagecolorallocatealpha($destination, 0, 0, 0, 127);
        imagefill($destination, 0, 0, $imageColor);
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $orig_image_name = "des_" . $time . ".png";
        return array($destination, $orig_image_name);
    }

    private function processLargeImages($large_image) {
        foreach ($large_image as $key => $value) {
            $decodedLargeImage[$key] = base64_decode($value);
        }
        $source = array();
        foreach ($decodedLargeImage as $key => $value) {
            $source[$key] = imagecreatefromstring($value);
        }
        return $source;
    }

    private function generateSVGImages($canvasSVG, $designId) {
        $svgImagesNames = array();
        $mediaPath = $this->dir->getPath('media');
        $svgDir = $mediaPath . '/productdesigner/designs/' . $designId . '/svg/';
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $index = 0;
        foreach ($canvasSVG as $svg_image) {
            $svg_name = "/svg_" . $index++ . "_" . $time . ".svg";
            $svgImagesNames[] = array('svg' => array('name' => $svg_name));
            try {
                if (!file_exists($svgDir)) {
                    mkdir($svgDir, 0777, true);
                }
                $fp = fopen($svgDir . $svg_name, 'w');
                fwrite($fp, $svg_image);
                fclose($fp);
            } catch (\Exception $e) {
                $response = $this->_infoHelper->throwException($e, self::class);
                $this->getResponse()->setBody(json_encode($response));
            }
        }
        return $svgImagesNames;
    }

    public function generatePDF($destination, $imageName, $designId, $orderId, $newName, $copyDestinationPath) {
        $destination = rtrim($destination, "/");
        $path = $destination . $imageName;
        $imgtype = getimagesize($path);
        switch ($imgtype['mime']) {
            case 'image/jpeg':
                $name = basename($imageName, '.jpg') . '.pdf';
                break;

            case 'image/png':
                $name = basename($imageName, '.png') . '.pdf';
                break;
            default:
                $name = basename($imageName, '.jpg') . '.pdf';
        }

        $size = 'A4';
        $cnt_height = ($imgtype[1] * 550) / $imgtype[0];
        if ($cnt_height < 800) {
            $size = 'A4';
        }
        if ($cnt_height > 800 && $cnt_height < 1150) {
            $size = 'A3';
        } else if ($cnt_height > 1150 && $cnt_height < 1650) {
            $size = 'A2';
        } else if ($cnt_height > 1650 && $cnt_height < 2350) {
            $size = 'A1';
        }
        if ($imgtype[0] >= 550) {
            $content = "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "' width='550'></div><page_footer><div style='text-align:right'>Order ID # " . $orderId . "</div></page_footer></page>";
        } else {
            $content = "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "'></div><page_footer><div style='text-align:right'>Order ID # " . $orderId . "</div></page_footer></page>";
        }
        $this->createPdf($content, $name, $size, $designId, $newName, $copyDestinationPath);
    }

    public function downloadAllImagesPdf($all_design_images, $designId, $orderId, $newName, $copyDestinationPath) {
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dir = $reader->getAbsolutePath() . 'productdesigner/designs/' . $designId . '/';
        $content = '';
        foreach ($all_design_images as $designImage) {
            foreach ($designImage as $key => $value) {
                if ($key == 'base_high') {
                    $path = $dir . 'base' . $value;
                } else {
                    $path = $dir . 'orig' . $value;
                }


                $imgtype = getimagesize($path);
                $size = 'A4';
                $cnt_height = ($imgtype[1] * 550) / $imgtype[0];
                if ($cnt_height < 800) {
                    $size = 'A4';
                }
                if ($cnt_height > 800 && $cnt_height < 1150) {
                    $size = 'A3';
                } else if ($cnt_height > 1150 && $cnt_height < 1650) {
                    $size = 'A2';
                } else if ($cnt_height > 1650 && $cnt_height < 2350) {
                    $size = 'A1';
                }
                if ($imgtype[0] >= 550) {
                    $content .= "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "' width='550'></div><page_footer><div style='text-align:right'>Order ID # " . $orderId . "</div></page_footer></page>";
                } else {
                    $content .= "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "'></div><page_footer><div style='text-align:right'>Order ID # " . $orderId . "</div></page_footer></page>";
                }
            }
        }
        $name = $orderId . '_designs.pdf';
        $this->createPdf($content, $name, $size, $designId, $newName, $copyDestinationPath);
    }

    public function createPdf($content, $name, $size, $designId, $newName, $copyDestinationPath) {
        $html2pdf = new Html2Pdf('P', $size, 'en');
        $html2pdf->WriteHTML($content);
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
        $path = $extractPath . 'designs/' . $designId . '/pdf/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $html2pdf->Output($path . $name, 'F');
        copy($path . $name, $copyDestinationPath . $name);
        rename($copyDestinationPath . $name, $copyDestinationPath . $newName . '.pdf');
    }


    public function createSvgZip($zip_path,$item_path,$name1){
        $name = $item_path .'/'. $name1 . '.zip';
        if (count($zip_path)) {
            if (file_exists($name)) {
                unlink($name);
            }
            $zip = new \ZipArchive();
            if ($zip->open($name, \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            foreach ($zip_path as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }
    }

    public function getOriginalImages($item_path, $design_id, $nameNumberIndex){
        if (!file_exists($item_path .'/namenumber')) {
            mkdir($item_path .'/namenumber', 0777, true);
        }
        $item_path = $item_path .'/namenumber';
        if (!file_exists($item_path .'/images')) {
            mkdir($item_path .'/images', 0777, true);
        }
        $designs = $this->_designFactory->create()->addFieldToFilter('design_id', array('eq' => $design_id));  
        foreach($designs as $des){
            $nameNumberDetails = json_decode($des->getNameNumberDetails(), true);
            $nameNumberDetails[$nameNumberIndex]['isGenerated'] = 'true';
            $des->setNameNumberDetails(json_encode($nameNumberDetails));
            $tmp = json_decode($des['json_objects'], true);
            foreach($tmp as $objects){
                $obj = $objects['objects'];
                foreach ($obj as $data) {
                    if(!empty($data['tab']) && $data['tab'] == 'clipart'){
                        $path = $item_path .'/'. 'images/' . 'clipart/';
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        $src = $data['src'];
                        $imageName = explode('/', $src);
                        $imageName = end($imageName);
                        copy($src,$path . $imageName);
                    }
                    if(!empty($data['tab']) && $data['tab'] == 'upload'){
                        $path = $item_path .'/'. 'images/' . 'upload/';
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        $src = $data['src'];
                        $imageName = explode('/', $src);
                        $imageName = end($imageName);
                        copy($src,$path . $imageName);
                    }
                }
            }
        }
        $designs->save();
    }
}
