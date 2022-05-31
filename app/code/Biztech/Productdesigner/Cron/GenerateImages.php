<?php

namespace Biztech\Productdesigner\Cron;

use \Spipu\Html2Pdf\Html2Pdf;

class GenerateImages {

    protected $designOrderCollection;
    protected $designOrder;
    protected $design;
    protected $dir;
    protected $designImages;
    protected $product;
    protected $selectionareaCollection;
    protected $order;
    protected $_storeManager;
    protected $pdHelper;
    protected $infoHelper;
    protected $configurable;
    protected $designImagesOrigDir;
    protected $designImagesFactory;
    protected $_designCollection;
    protected $_fileSystem;
    protected $_scopeConfig;
    protected $_eventManager;
    protected $_objectFactory;
    protected $_orderHelper;
    protected $_mediaGallery;
    protected $_side;


    const DPI = 150;
    const CANVASWIDTH = 540;

    public function __construct(
    \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrderCollection, \Biztech\Productdesigner\Model\DesignOrdersFactory $designOrder, \Biztech\Productdesigner\Model\DesignsFactory $design, \Magento\Framework\Filesystem\DirectoryList $dir, \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designImages, \Magento\Catalog\Model\ProductFactory $product, \Biztech\Productdesigner\Model\Mysql4\Selectionarea\CollectionFactory $selectionareaCollection, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $pdHelper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable, \Biztech\Productdesigner\Model\DesignimagesFactory $designImagesFactory, \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designCollection, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Event\Manager $manager, \Magento\Framework\DataObjectFactory $objectFactory, \Biztech\Productdesigner\Helper\Order $orderHelper, \Biztech\Productdesigner\Model\ResourceModel\MediaGallery\CollectionFactory $mediaGallery, \Biztech\Productdesigner\Model\Side $side
    ) {
        $this->designOrderCollection = $designOrderCollection;
        $this->designOrder = $designOrder;
        $this->design = $design;
        $this->dir = $dir;
        $this->designImages = $designImages;
        $this->product = $product;
        $this->selectionareaCollection = $selectionareaCollection;
        $this->_storeManager = $storeManager;
        $this->pdHelper = $pdHelper;
        $this->infoHelper = $infoHelper;
        $this->configurable = $configurable;
        $this->designImagesFactory = $designImagesFactory;
        $this->_designCollection = $designCollection;
        $this->_fileSystem = $fileSystem;
        $this->_scopeConfig = $scopeConfig;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        $this->_orderHelper = $orderHelper;
        $this->_mediaGallery = $mediaGallery;
        $this->_side = $side;
    }

    public function execute() {
        $designId = '';
        try {
            $orderData = $this->designOrderCollection->create()
                    ->addFieldToFilter('status', array('eq' => 0))
                    ->getFirstItem();
            $order_row = $orderData->getData();
            if (count($order_row) != 0) {
                $designId = $order_row['design_id'];
                $orderId = $order_row['order_id'];
                $orderImagesId = $order_row['id'];
                $orderDesignModel = $this->designOrder->create()->load($orderImagesId);
                $orderDesignModel->setStatus('1'); // Processing
                $orderDesignModel->save();
                $design = $this->design->create()->load($designId);
                $designImagesCollection = $this->designImages->create()
                        ->addFieldToFilter('design_id', $designId)
                        ->addFieldToFilter('design_image_type', array('in' => array('base_high', 'orig_high')));
                $designImagesCollection->walk('delete');
                try {
                    $output_result = $this->generateProductOutputImages($design, $orderId);
                    $result = $this->generateItemOrderImages($orderId,$designId);
                } catch (\Exception $e) {
                    $output_result['status'] = 'fail';
                    $output_result['error'] = $e->getMessage();
                    $response = $this->infoHelper->throwException($e, self::class);
                    $this->getResponse()->setBody(json_encode($response));
                }

                $isImagesGenerated = 3; // Failure
                if (isset($output_result['status']) && $output_result['status'] == 'success') {
                    $isImagesGenerated = 2; // Success
                }
                $orderDesignModel->setStatus($isImagesGenerated);
                $orderDesignModel->save();                
            }
        } catch (\Exception $e) {
            $response = $this->infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
        return $this;
    }

    public function generateDesignPaths($design, $mediaPath) {
        $designId = $design->getId();
        $designImagesBaseDir = $mediaPath . '/productdesigner/designs/' . $designId . '/base/';
        $designImagesOrigDir = $mediaPath . '/productdesigner/designs/' . $designId . '/orig/';
        if (!file_exists($designImagesBaseDir)) {
            mkdir($designImagesBaseDir, 0777, true);
        }
        if (!file_exists($designImagesOrigDir)) {
            mkdir($designImagesOrigDir, 0777, true);
        }
        return array($designImagesBaseDir, $designImagesOrigDir);
    }

    public function getCanvasDataUrl($design, $mediaPath) {
        $large_image_file = $design->getCanvasDataurlFile();
        $dir = $mediaPath . '/productdesigner/canvasData/';
        $filename = $dir . $large_image_file;
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
        return $merged_large_images;
    }

    public function generateProductOutputImages($design, $orderId) {
        $designId = $design->getId();
        $mediaPath = $this->dir->getPath('media');
        $productId = $design->getProductId();
        $associatedProductId = $design->getAssociatedProductId();
        /**
         * It will generate the paths if not exist
         */
        list($designImagesBaseDir, $designImagesOrigDir) = $this->generateDesignPaths($design, $mediaPath);
        /**
         * It will fetch canvas data url by reading text file generated and saved in design table
         */
        $merged_large_images = $this->getCanvasDataUrl($design, $mediaPath);

        /**
         * It will fetch product image of products
         */
        $productData = $this->infoHelper->getProductTypeAndMediaImages($productId);
        $grouped_product_images = $productData['media_image'];
        $product_type = $productData['product_type'];
        /**
         * It will fetch all design areas of product
         */
        $selectionareas = $this->selectionareaCollection->create()
                        ->addFieldToFilter('product_id', array('in' => array($associatedProductId, $productId)))->getData();
        $dimensions = array();
        foreach ($selectionareas as $selectionarea) {
            $dimensions[$selectionarea['image_id']][$selectionarea['design_area_id']] = $selectionarea;
        }
        /**
         * Relative image id in case of configurable product
         */
        if ($product_type == 'configurable') {
            $relativeImageIds = json_decode($design->getRelativeImageId(), true);
            $relatedImageIdsArray = array();
            foreach ($relativeImageIds as $current => $desired) {
                $imageIdWithDesignAreasArray = explode("&", $current);
                $imageIds = $imageIdWithDesignAreasArray[0];
                $imageIdArray = explode('@', $imageIds);
                $imageId = $imageIdArray[1];
                $designAreaId = $imageIdWithDesignAreasArray[1];

                $desiredImageIdWithDesignAreasArray = explode("&", $desired);
                $desiredImageIds = $desiredImageIdWithDesignAreasArray[0];
                $desiredImageIdArray = explode('@', $desiredImageIds);
                $desiredImageId = $desiredImageIdArray[1];
                $desiredDesignAreaId = $desiredImageIdWithDesignAreasArray[1];

                $relatedImageIdsArray['image_ids'][$imageId] = $desiredImageId;
                $relatedImageIdsArray['designArea_ids'][$designAreaId] = $desiredDesignAreaId;
            }
            $parentImageIds = json_decode($design->getParentImageId(), true);
        }
        /**
         * Loop for all large images
         */
        $params = array();
        $params['designImagesBaseDir'] = $designImagesBaseDir;
        $params['associatedProductId'] = $associatedProductId;
        $params['designImagesOrigDir'] = $designImagesOrigDir;
        $params['design_id'] = $designId;
        $params['productId'] = $productId;
        $params['canvasRatio'] = \Biztech\Productdesigner\Helper\Info::CanvasRatio;
        $all_design_images = array();
        foreach ($merged_large_images as $productImageId => $large_image) {
            /**
             * Change key to updated
             */
            if ($product_type == 'configurable') {
                $image_id = $relatedImageIdsArray['image_ids'][$productImageId];
                $params['relatedImageIdsArray'] = $relatedImageIdsArray['designArea_ids'];
            } else {
                $image_id = $productImageId;
                $params['relatedImageIdsArray'] = array();
            }
            $prod_image_path = $grouped_product_images[$image_id]['path'];
            $params['prod_image_path'] = $prod_image_path;
            $params['large_image'] = $large_image;
            $params['image_id'] = $image_id;
            /**
             * If associated dimensions are not set then fetch from parent
             */
            if (!isset($dimensions[$image_id])) {
                $image_id = $parentImageIds[$productImageId];
            }
            $params['dimensions'] = $dimensions[$image_id];
            $all_design_images[] = $this->generateDesignImages($params);
            foreach ($all_design_images as $images) {
                foreach ($images as $key => $value) {
                    if ($key == 'base_high') {
                        $this->generatePDF($designImagesBaseDir, $value, $designId, $orderId);
                    } else {
                        $this->generatePDF($designImagesOrigDir, $value, $designId, $orderId);
                    }
                }
            }
            $this->downloadAllImagesPdf($all_design_images, $designId, $orderId);
            $data['image_id'][] = $image_id;
        }
        $data['designId'] = $designId;
        return $this->saveGeneratedImages($all_design_images, $data);
    }

    public function downloadAllImagesPdf($all_design_images, $designId, $orderId) {
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
        $this->createPdf($content, $name, $size, $designId);
    }

    public function generateDesignImages($params) {
        /**
         * It will format large image data
         */
        $source = $this->processLargeImages($params['large_image'], $params['relatedImageIdsArray']);
        /**
         * Initialize variable from params
         */
        $designImagesBaseDir = $params['designImagesBaseDir'];
        $designImagesOrigDir = $params['designImagesOrigDir'];
        $allDimensions = $params['dimensions'];
        $productId = $params['productId'];
        $associatedProductId = $params['associatedProductId'];
        $canvasRatio = $params['canvasRatio'];
        $prod_image_path = $params['prod_image_path'];
        list($resize_width, $resize_height) = $this->infoHelper->calculateResizeWidthHeight($prod_image_path);
        /** Start Added By A.S. Custom Size Output * */
        $customObject = $this->_objectFactory->create();
        $customObject->setImage(array('imagepath' => $prod_image_path, 'product_id' => $associatedProductId, 'base_path' => $designImagesBaseDir, 'source' => $source, 'allDimensions' => $allDimensions));
        $this->_eventManager->dispatch('generated_images_custom_size', ['imagedata' => $customObject]);
        $prod_image_path = $customObject->getImage('imagepath') ?: $params['prod_image_path'];
        $source = $customObject->getImage('source');
        /** End Added By A.S. Custom Size Output * */
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

    public function generatePDF($destination, $imageName, $designId, $orderId) {
        $destination = rtrim($destination, "/");
        $path = $destination . $imageName;
        $imgtype = getimagesize($path);
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
        $this->createPdf($content, $name, $size, $designId);
    }

    public function createPdf($content, $name, $size, $designId) {
       $html2pdf = new Html2Pdf('P', $size, 'en');
       $html2pdf->WriteHTML($content);
       $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
       $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
       $path = $extractPath . 'designs/' . $designId . '/pdf/';
       if (!file_exists($path)) {
           mkdir($path, 0777, true);
       }
       $html2pdf->Output($path . $name, 'F');
    }

    public function createBlankImage($iWidth, $iHeight) {
        $destination = imagecreatetruecolor($iWidth, $iHeight);
        imagesavealpha($destination, true);
        $imageColor = imagecolorallocatealpha($destination, 0, 0, 0, 127);
        imagefill($destination, 0, 0, $imageColor);
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $orig_image_name = "des_" . $time . ".png";
        return array($destination, $orig_image_name);
    }

    public function processLargeImages($large_image, $relatedImageIdsArray) {
        foreach ($large_image as $key => $value) {
            if (count($relatedImageIdsArray) > 0) {
                $newKey = $relatedImageIdsArray[$key];
            } else {
                $newKey = $key;
            }
            $decodedLargeImage[$newKey] = base64_decode($value);
        }
        $source = array();
        foreach ($decodedLargeImage as $key => $value) {
            $source[$key] = imagecreatefromstring($value);
        }
        return $source;
    }

    public function saveGeneratedImages($all_design_images, $data) {
        $result = array();
        try {
            foreach ($all_design_images as $index => $design_images) {
                $image_id = $data['image_id'][$index];
                foreach ($design_images as $image_type => $design_image) {
                    $designImagesModel = $this->designImagesFactory->create();
                    $designImagesModel->setDesignId($data['designId'])
                            ->setDesignImageType($image_type)
                            ->setProductImageId($image_id)
                            ->setImagePath(str_replace('\\', '/', $design_image));
                    $designImagesModel->save();
                }
            }
            $eventParams = array("designedImages" => $all_design_images, "designId" => $data['designId'], "defaultDpiValue" => self::DPI);
            $this->_eventManager->dispatch('generated_images_saved_after', ['eventData' => $eventParams]);
            $result['status'] = 'success';
        } catch (\Exception $e) {
            $result['status'] = 'failure';
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    public function generateItemOrderImages($order_id,$designs_id){
        $designIds = array();
        $orders = $this->designOrderCollection->create()->addFieldToFilter("order_id",$order_id)->getData();
        foreach ($orders as $orderkey => $ordervalue) {
            if(!empty($ordervalue['design_id'])){
               array_push($designIds, $ordervalue['design_id']);
            }
        }                


        foreach ($designIds as $designKey => $designValue) {
            $designs_id = $designValue;
            $order = $this->designOrderCollection->create()->addFieldToFilter("design_id",$designs_id)->getFirstItem()->getData();
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
            $designData = $this->design->create()->load($designs_id)->getData();
            $product = $this->product->create()->load($designData['associated_product_id']);
            $sku = $product->getSku();
            $item_path = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $itemID;
            $zip_path = array();

            if (!file_exists($item_path)) {
                mkdir($item_path, 0777, true);
            }
            $content = '';
            $design_id = $designs_id;
            $this->_orderHelper->getOriginalImages($item_path, $design_id);
            $designImages = $this->designImages->create()->addFieldToFilter('design_id', array('eq' => $design_id));
            $generateVectoreFiles  = [];
            foreach ($designImages as $designImage) {

                $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/mylog.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $response['log'] = $designImage->getDesignImageType();
                $logger->info($response['log']);

                if ($designImage->getTemplateMediaId()) {
                    $side = $this->infoHelper->getImageSideFromTemplateMedia($designImage->getTemplateMediaId(), true, $this->_mediaGallery, $this->_side);
                } else {
                    $side = $this->infoHelper->getImageSideFromTemplateMedia($designImage->getProductImageId(), false, $this->_mediaGallery, $this->_side);
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
                    $generatedName = $generatedNumber = "";
                    $svgNname = $designImage->getImagePath();
                    $designImagesSVGDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/svg/';
                    $filecontent = $designImagesSVGDir.$svgNname;

                    $fileData = file_get_contents($filecontent);


                    /*Name and Number Testing*/

                        $design = $this->design->create()->load($design_id);
                        $designData = $design->getData();
                        if(!empty($designData['name_number_details'])){
                         $nameNumbrDetails = json_decode($designData['name_number_details'],true);
                         foreach ($nameNumbrDetails as $key => $value) {
                            $svgObj = new \SimpleXMLElement($fileData);
                            $svgObj->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
                            foreach ($svgObj->xpath('//svg:text') as $attrkey => $attrvalue) {
                                if($attrvalue->tspan == $value['name']){
                                    $generatedName = $value['name'];
                                }
                                if($attrvalue->tspan == $value['number']){
                                    $generatedNumber = $value['number'];
                                }
                            }
                        }


                         /* Read and replace name number */
                         foreach ($nameNumbrDetails as $key => $value) {
                                $content = file_get_contents($filecontent);
                                $svg = new \SimpleXMLElement($content);
                                $svg->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
                                foreach ($svg->xpath('//svg:text') as $attrkey => $attrvalue) {
                                    if($attrvalue->tspan == $generatedName){
                                        $attrvalue->tspan = $value['name'];
                                    }
                                    if($attrvalue->tspan == $generatedNumber){
                                        $attrvalue->tspan = $value['number'];
                                    }
                                }
                                $time = substr(base64_encode(microtime()), rand(0, 26), 7);

                                $svg_name = 'Svg-' . $sku . '-'. $side.'-'.$value['number'].'.svg';
                                $svg->asXML($designImagesSVGDir.$svg_name);

                                /*Geneare single combine vectore files*/
                                //array_push($generateVectoreFiles, '/'.$svg_name);

                                $generateVectoreFiles['vectore-svg-'.$value['id']][] = '/'.$svg_name;
                         }


                         /*Create Zip for namenumber svg files  for hotfolder*/
                         if(!file_exists($item_path . '/namenumber')){
                             mkdir($item_path . '/namenumber', 0777, true);
                         }
                         $files = scandir($designImagesSVGDir);
                         foreach($files as $key=>$value){
                             $iscmyk = explode("_", $value);
                             $isOldSvg = !empty($iscmyk) ? $iscmyk[0] : "";
                             $iscmyk = $iscmyk[count($iscmyk)-1];
                             if($value!='.' && $value!='..' && $iscmyk != 'cmyk.svg' && $value != $svgNname && $isOldSvg != 'svg'){

                                 $zip_path[] = $designImagesSVGDir . $value;
                             }
                         }
                         $name = 'Print-'.$sku;
                         $this->createSvgZip($zip_path, $item_path . '/namenumber/', $name);
                     }

                    /*End Name and Number Testing*/


                    $svgArr[] = scandir($designImagesSVGDir, 1);
                    foreach ($svgArr as $files) {
                        foreach ($files as $key => $value) {
                                if ('/' . $value == $svgNname) {
                                    $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                                    $svgOldName = $imagePreFix . '.svg';
                                    $svgname1 = 'Svg-' . $sku . '-' . $side;
                                    copy($designImagesSVGDir . $svgOldName, $designImagesSVGDir . '/' . $svgname1.'.svg');
                                    $zipPath[] = $designImagesSVGDir . '/' . $svgname1 . '.svg';
                                }
                        }
                    }
                        $name = 'Print-'.$sku;
                        $this->createSvgZip($zipPath, $item_path , $name);
                }
            }


            /* Diaptch event for create vectore files*/
            if(!empty($generateVectoreFiles)){
                $this->_eventManager->dispatch('generate_namenumber_vectore_file', ['imagedata' => $generateVectoreFiles,'designId' => $design_id]);
            }

            /*Copy Vectore pdf*/
            $designImagesPDFDir = $reader->getAbsolutePath() . '/productdesigner/designs/' . $design_id . '/pdf/';
            $pdfArr[] = scandir($designImagesPDFDir, 1);
            foreach ($pdfArr as $files) {
                foreach ($files as $key => $value) {
                    $getFileName = explode('_', $value);
                    if($value != "." && $value != ".." && !empty($value) && $getFileName[0] == $design_id){
                        $imagePreFix = ltrim(strstr($value, '.', true), "'\'");
                        $pdfOldName = $imagePreFix . '.pdf';
                        $pdfname1 = 'Vector-pdf-' . $sku . '-' . $side;
                        copy($designImagesPDFDir . $pdfOldName, $item_path . '/' . $pdfOldName);
                        rename($item_path . '/' . $pdfOldName, $item_path . '/' . $pdfname1 . '.pdf');
                    }
                }
            }

            /*Remove temporary generated images*/
            // foreach ($zipPath as $rmvalue) {
            //     if(file_exists($rmvalue)){
            //         unlink($rmvalue);
            //     }
            // }

            $this->_orderHelper->generateCSV($item_path, $order, $order_id, $designs_id, 'false');
            $zip_dir = $reader->getAbsolutePath() . 'productdesigner/order/' . $order_increment_id . '/' . $itemID;
            $zipName = $order_increment_id . '_' . $itemID . '_' . addslashes($sku);
            /*$this->_orderHelper->createEntireFolderZip($item_path, $zipName, $zip_dir);*/
        }

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

}
