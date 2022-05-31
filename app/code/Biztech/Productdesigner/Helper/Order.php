<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 */

namespace Biztech\Productdesigner\Helper;

use \Spipu\Html2Pdf\Html2Pdf;

class Order extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_order;
    protected $_fileSystem;
    protected $_designFactory;
    protected $_infoHelper;
    protected $_eventManager;
    protected $_designOrdersFactory;
    protected $_productLoader;
    protected $_designsFactory;
    protected $_objectFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Filesystem $fileSystem,
        \Biztech\Productdesigner\Model\Mysql4\Designs\CollectionFactory $designFactory,
        \Biztech\Productdesigner\Helper\Info $infoHelper,
        \Magento\Framework\Event\Manager $manager,
        \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrdersFactory,
        \Magento\Catalog\Model\ProductFactory $_productLoade,
        \Biztech\Productdesigner\Model\DesignsFactory $designsFactory,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_order = $order;
        $this->_fileSystem = $fileSystem;
        $this->_designFactory = $designFactory;
        $this->_infoHelper = $infoHelper;
        $this->_eventManager = $manager;
        $this->_designOrdersFactory = $designOrdersFactory; 
        $this->_productLoader = $_productLoade;
        $this->_designsFactory = $designsFactory;
        $this->_objectFactory = $objectFactory;
    }



    //GET ORIGINAL IMAGES
    public function getOriginalImages($item_path, $design_id) {
        if (!file_exists($item_path)) {
            if (!file_exists($item_path . '/' . 'images')) {
                mkdir($item_path . '/' . 'images', 0777, true);
            }
        }

        $designs = $this->_designFactory->create()->addFieldToFilter('design_id', array('eq' => $design_id));
        foreach ($designs as $des) {
            $tmp = json_decode($des['json_objects'], true);
            foreach ($tmp as $objects) {
                $obj = $objects['objects'];
                foreach ($obj as $data) {
                    if (!isset($data['tab'])) {
                        continue;
                    }
                    if ($data['tab'] == 'clipart') {
                        $path = $item_path . '/' . 'images/' . 'clipart/';
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                    }
                    if ($data['tab'] == 'upload') {
                        $path = $item_path . '/' . 'images/' . 'upload/';
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                    }
                    if ($data['tab'] == 'clipart' || $data['tab'] == 'upload') {
                        $src = $data['src'];
                        $finalsrc = $this->_infoHelper->convertRelToAbsPath($src);
                        $imageName = explode('/', $src);
                        $imageName = end($imageName);
                        copy($finalsrc, $path . $imageName);
                    }
                }
            }
        }
    }

    //GENERATE PDF
    public function generatePDF($destination, $imageName, $designId, $side, $item_path, $order_increment_id, $pdfname) {
        $path = $destination . $imageName;
        $imgtype = getimagesize($path);
        $imgtype = getimagesize($path);

        $size = 'A4';
        $cnt_height = ($imgtype[1] = $imgtype[1] * 550) / $imgtype[0];
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
            $content = "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "' width='550'></div><page_footer><div style='text-align:right'>Order ID # " . $order_increment_id . "</div></page_footer></page>";
        } else {
            $content = "<page><div style='margin:0 auto; text-align:center; vertical-align:middle;'><img src='" . $path . "'></div><page_footer><div style='text-align:right'>Order ID # " . $order_increment_id . "</div></page_footer></page>";
        }
        $this->createPdf($content, $pdfname, $size, $designId, $item_path);
    }

    //CREATE PDF
    public function createPdf($content, $name, $size, $designId, $item_path) {
        $name = $name . '.pdf';
        $html2pdf = new Html2Pdf('P', $size, 'en');
        $html2pdf->WriteHTML($content);
        $reader = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $extractPath = $reader->getAbsolutePath() . 'productdesigner/';
        $path = $item_path . '/';
        $html2pdf->Output($path . $name, 'F');
    }

    //CREATE ZIP FOR DOWNLOAD ORDER
    public function createEntireFolderZip($order_dir, $order_increment_id, $zip_dir) {
        if ($order_dir[strlen($order_dir) - 1] != "/") {
            $order_dir .= "/";
        }
        if (file_exists($order_dir . 'details.zip')) {
            unlink($order_dir . 'details.zip');
        }
        $zip = new \ZipArchive;

        // Create Our Zip file
        $zip->open($order_dir . 'details.zip', \ZipArchive::CREATE);
        $all = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($zip_dir), \RecursiveIteratorIterator::SELF_FIRST);

        $parentPath = '';

        $MAXLENGTH = 130000000;
        // Traverse each file one by one
        foreach ($all as $file) {

            // Fetch path first
            $file = str_replace('\\', '/', $file);

            // Remove system path from the same
            $realPath = str_replace($order_dir, '', $file);

            // As Recursive returns all the folders along with '.' and '..'
            // We won't process on those folders hence we will contiue execution
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }

            // If it's directory,
            // add it to zip
            if (is_dir($file)) {
                $zip->addEmptyDir($realPath);
            }

            // if it's file
            // fetch parent path
            // combine it with file name
            // add it to zip
            else if (file_exists($file)) {
                $parentPath = substr($realPath, 0, strrpos($realPath, "/"));
                $zip->addFromString($parentPath . DIRECTORY_SEPARATOR . basename($realPath), file_get_contents($file));
            }
        }
        $zip->close();
        $zipFile = $order_dir . 'details.zip';
        $zipName = $order_increment_id . '_details.zip';
        header("Content-type: application/zip");
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header("Content-length: " . filesize($zipFile));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($zipFile);
    }

    //GENERATE CSV FILE FOR ORDER
    public function generateCSV($zip_dir, $order, $order_id, $item_id, $isItemLevel) {
        // get order by increment id
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Sales\Model\Order');
        $order = $collection->loadByIncrementId($order_id)->getData();
        
        $orderData = $this->_order->load($order['entity_id']);
        $billingAddress = $orderData->getBillingAddress()->getData();
        if (isset($shippingAddress)) {
            $shippingAddress = $orderData->getShippingAddress()->getData();
            $address = $shippingAddress['street'] . ',' . $shippingAddress['city'] . ',' . $shippingAddress['region'] . ',' . $shippingAddress['postcode'] . ',' . $shippingAddress['country_id'];
        } else {
            $address = $billingAddress['street'] . ',' . $billingAddress['city'] . ',' . $billingAddress['region'] . ',' . $billingAddress['postcode'] . ',' . $billingAddress['country_id'];
        }

        $name = $zip_dir . '/' . 'details.csv';
        $file = fopen($name, 'w');
        fputcsv($file, array('Order Number ', 'Item Number', 'Item Name', ' Item SKU', 'Item Quantity', 'Item Price', 'Subtotal Price', 'Shipping Address'));
        $details = array();
        $tmp = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        $currencySymbol = $currency->getCurrencySymbol();

        if (isset($item_id) && $isItemLevel == 'true') {
            foreach ($orderData->getAllVisibleItems() as $item) {
                if ($item->getId() == $item_id) {
                    $subtotal_price = $item->getPrice() * $item['product_options']['info_buyRequest']['qty'];
                    $tmp['order_id'] = $order_id;
                    $tmp['item_id'] = $item->getQuoteItemId();
                    $tmp['item_name'] = $item->getName();
                    $tmp['sku'] = $item->getSku();
                    $tmp['qty'] = $item['product_options']['info_buyRequest']['qty'];
                    $tmp['price'] = $currencySymbol . $item->getPrice();
                    $tmp['subtotal_price'] = $currencySymbol . $subtotal_price;
                    $tmp['shippingAddress'] = $address;
                    array_push($details, $tmp);
                }
            }
        } else {
            foreach ($orderData->getAllVisibleItems() as $item) {
                $subtotal_price = $item->getPrice() * $item['product_options']['info_buyRequest']['qty'];
                $tmp['order_id'] = $order_id;
                $tmp['item_id'] = $item->getQuoteItemId();
                $tmp['item_name'] = $item->getName();
                $tmp['sku'] = $item->getSku();
                $tmp['qty'] = $item['product_options']['info_buyRequest']['qty'];
                $tmp['price'] = $currencySymbol . $item->getPrice();
                $tmp['subtotal_price'] = $currencySymbol . $subtotal_price;
                $tmp['shippingAddress'] = $address;
                array_push($details, $tmp);
            }
        }
        foreach ($details as $row) {
            fputcsv($file, $row);
        }
    }


    //GENERATE CSV FILE FOR API ORDER
    public function generateApiCSV($zip_dir, $order, $order_id, $designs_id, $isItemLevel) {

        $order = $this->_designOrdersFactory->create()->addFieldToFilter("design_id",$designs_id)->getFirstItem()->getData();
        $order_increment_id = $order['order_id'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Sales\Model\Order'); 
        $order = $collection->loadByIncrementId($order_increment_id)->getData();
        if(!empty($designs_id)){
            $designData = $this->_designsFactory->create()->load($designs_id)->getData();
            $product = $this->_productLoader->create()->load($designData['associated_product_id']);
        }
        $order_increment_id = $this->_order->load($order_id)->getIncrementId();
        $orders = $this->_designOrdersFactory->create()->addFieldToFilter("order_id",$order_increment_id)->getData();

        $customObject = $this->_objectFactory->create();
        $orderData = $this->_eventManager->dispatch('getOrderData', ['orderId' => $order_id,'customObject' => $customObject]);
        $addressData = $address = "";
        $apiOrder=$customObject->getApiOrderData();
        if($apiOrder){
            $addressData = json_decode($apiOrder['order_shipping_address'],true);
            
            if($addressData){
                foreach($addressData as $key => $value){
                    if($value !=''){
                     $address= $this->preparAddress($address , $value);
                    }
                }
            }
        }


        $name = $zip_dir . '/' . 'details.csv';
        $file = fopen($name, 'w');
        fputcsv($file, array('Order Number ', 'Item Name', ' Item SKU', 'Item Quantity', 'Item Price','Subtotal Price','Shipping Address'));
        $details = array();
        $tmp = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode(); 
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        $currencySymbol = $currency->getCurrencySymbol();

        if(isset($designs_id) && $isItemLevel == 'true'){
                    $subtotal_price = ($order['order_item_price'])*($order['order_item_qty']);
                    $tmp['order_id'] = $order_increment_id;
                    $tmp['item_name'] = $product->getName();
                    $tmp['sku'] = $product->getSku();
                    $tmp['qty'] = $order['order_item_qty'];
                    $tmp['price'] = $currencySymbol.$order['order_item_price'];
                    $tmp['subtotal_price'] = $currencySymbol.$subtotal_price;
                    $tmp['shippingAddress'] = $address;
                    array_push($details, $tmp);
        } else {
            foreach ($orders as $item) {
                $designData = $this->_designsFactory->create()->load($item['design_id'])->getData();
                $product = $this->_productLoader->create()->load($designData['associated_product_id']);
                $subtotal_price = ($item['order_item_price'])*($item['order_item_qty']);
                $tmp['order_id'] = $order_increment_id;
                $tmp['item_name'] = $product->getName();
                $tmp['sku'] = $product->getSku();
                $tmp['qty'] = $item['order_item_qty'];
                $tmp['price'] = $currencySymbol.$item['order_item_price'];
                $tmp['subtotal_price'] = $currencySymbol.$subtotal_price;
                $tmp['shippingAddress'] = $address;
                array_push($details, $tmp);
            }
        }
        foreach ($details as $row) {
            fputcsv($file, $row);
        }
    }

    //PREPARE ADDRESS DATA
    public function preparAddress($add,$data){
        try {
          if($add=='' ){
                $add = $data;
            } else{
                $add = $add .", ".$data;
            }
        return $add;  
        } catch (\Exception $e) {
            throw new \Exception("Error while prepare address data in order".$e->getMessage());
            
        }
    }
}
