<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Productdetail extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $productModel;
    protected $cartHelper;
    protected $stockItemRepository;
    protected $imageHelper;
    protected $wishlistHelper;
    protected $reviewSummaryModel;
    protected $helperOutput;
    protected $productFactory;
    protected $swatchHelper;
    protected $downloadableFactory;
    protected $frameworkRegistry;
    protected $formKey;
    protected $stockApiRepository;
    protected $_eventManager;
    protected $_objectFactory;

    /**
     * @param Context                                                            $context
     * @param JsonFactory                                                        $jsonFactory
     * @param Http                                                               $request
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManager
     * @param \Magento\Catalog\Model\Product                                     $productModel
     * @param \Magento\Catalog\Model\ProductFactory                              $productFactory
     * @param \Biztech\Magemobcart\Helper\Data                                   $cartHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository          $stockItemRepository
     * @param \Magento\Catalog\Helper\Image                                      $imageHelper
     * @param \Magento\Wishlist\Helper\Data                                      $wishlistHelper
     * @param \Magento\Review\Model\Review\Summary                               $reviewSummaryModel
     * @param \Magento\Catalog\Helper\Output                                     $helperOutput
     * @param \Magento\Swatches\Helper\Data                                      $swatchHelper
     * @param \Magento\Downloadable\Model\ResourceModel\Sample\CollectionFactory $downloadableFactory
     * @param \Magento\Framework\Registry                                        $frameworkRegistry
     * @param StockRegistryInterface                                             $stockRegistry
     * @param \Magento\Framework\Data\Form\FormKey                               $formKey
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface               $stockApiRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Review\Model\Review\Summary $reviewSummaryModel,
        \Magento\Catalog\Helper\Output $helperOutput,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Downloadable\Model\ResourceModel\Sample\CollectionFactory $downloadableFactory,
        \Magento\Framework\Registry $frameworkRegistry,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockApiRepository
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_productModel = $productModel;
        $this->_productFactory = $productFactory;
        $this->_cartHelper = $cartHelper;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_imageHelper = $imageHelper;
        $this->_wishlistHelper = $wishlistHelper;
        $this->_reviewSummaryModel = $reviewSummaryModel;
        $this->_helperOutput = $helperOutput;
        $this->_swatchHelper = $swatchHelper;
        $this->_downloadableFactory = $downloadableFactory;
        $this->_frameworkRegistry = $frameworkRegistry;
        $this->_stockRegistry = $stockRegistry;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        $this->_stockApiRepository = $stockApiRepository;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        parent::__construct($context);
    }

    /**
     * This function is used for get product details.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            try {
                $storeId = $postData['storeid'];
                if (array_key_exists('productid', $postData)) {
                    $productId = $postData['productid'];
                }
                if (array_key_exists('sku', $postData)) {
                    $productSku = $postData['sku'];
                } else {
                    $productSku = "";
                }

                if (!array_key_exists('productid', $postData)) {
                    if ($productSku != "") {
                        $productId = $this->_productFactory->create()->getIdBySku($productSku);
                    }
                }
                if (isset($productId) && $productId != null) {
                    $product = $this->_productFactory->create()->setStoreId($storeId)->load($productId);
                    if (empty($product)) {
                        $productResultArr = array(
                        'status' => 'message',
                        'message' => 'No product found.'
                        );
                        $jsonResult->setData($returnExtensionArray);
                        return $jsonResult;
                    }
                    $status = $product->getStatus();
                    $productStockData = $this->_stockApiRepository->getStockItem($productId);

                    $pro_qty = $productStockData->getQty();
                    $isInStock = $productStockData->getIsInStock();
                    $salebleModelData = $this->getSalebleQty($product->getSku());
                    if (array_key_exists('qty', $salebleModelData)) {
                        $pro_qty = $salebleModelData['qty'];
                    }
                    $stock_item = $productStockData;
                    if ($stock_item->getBackorders() != 0) {
                        if ($isInStock == 0) {
                            $isInStock = '0';
                        } else {
                            $isInStock = '1';
                        }
                    } else {
                        if ($pro_qty < 0 || $isInStock == 0) {
                            $pro_qty = 'Out of Stock';
                            $isInStock = '0';
                        } else {
                            $isInStock = '1';
                        }
                    }
                    $images = array();
                    $summaryData = $this->getProductRatingSummary($storeId, $productId);
                    $_images = $product->getMediaGalleryImages();
                    if ($_images) {
                        foreach ($_images as $_image) {
                            $label = $_image->getLabel();
                            if (strpos($label, 'swatch') !== false) {
                                continue;
                            } else {
                                $images[] = $this->_imageHelper->init($product, 'product_page_image_large')
                                ->setImageFile($_image->getFile())->constrainOnly(false)->keepAspectRatio(true)->keepFrame(true)->resize(300, 330)
                                ->getUrl();
                            }
                        }
                    }
                    $associated_products = array();
                    if ($product->getTypeId() == 'grouped') {
                        $associated_products = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    } elseif ($product->getTypeId() == 'configurable') {
                        $associated_products = $product->getTypeInstance()->getUsedProducts($product);
                    } elseif ($product->getTypeId() == 'bundle') {
                        $associated_products = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
                    }
                    $prices = array();
                    $associated_products_details = array();
                    $associated_products_list = array();
                    $related_products_list = array();
                    foreach ($associated_products as $associated_product1) {
                        $prices[] = $associated_product1->getPrice();
                    }
                    $mainfinalPrice = $product->getPrice();
                    if ($product->getTypeId() == 'configurable') {
                        if (!empty($prices)) {
                            $mainfinalPrice = min($prices);
                        }
                    }
                    if ($product->getTypeId() == 'grouped') {
                        if (!empty($prices)) {
                            $mainfinalPrice = min($prices);
                        }
                    }
                    $associated_products_related = $product->getRelatedProducts();
                    if (!empty($associated_products_related)) {
                        foreach ($associated_products_related as $associated_product) {
                            $associatedPrices = array();
                            $priceAssociateProduct = $this->_productFactory->create()->setStoreId($storeId)->load($associated_product->getId());
                            $finalPrice = $priceAssociateProduct->getPrice();
                            if ($associated_product->getTypeId() == 'configurable') {
                                $associated_products_price = $priceAssociateProduct->getTypeInstance()->getUsedProducts($priceAssociateProduct);
                                foreach ($associated_products_price as $associated_product2) {
                                    $associatedPrices[] = $associated_product2->getFinalPrice();
                                }
                                if (!empty($associatedPrices)) {
                                    $finalPrice = min($associatedPrices);
                                }
                            }
                            if ($associated_product->getTypeId() == 'grouped') {
                                $associated_products_price = $priceAssociateProduct->getTypeInstance(true)->getAssociatedProducts($priceAssociateProduct);
                                foreach ($associated_products_price as $associated_product2) {
                                    $associatedPrices[] = $associated_product2->getFinalPrice();
                                }
                                if (!empty($associatedPrices)) {
                                    $finalPrice = min($associatedPrices);
                                }
                            }
                            $associated_productStockData = $this->_stockApiRepository->getStockItem($associated_product->getId());
                            $qty = $associated_productStockData->getQty();

                            if ($qty < 0 || $associated_productStockData->getIsInStock() == 0) {
                                $qty = 'Out of Stock';
                            } else {
                                $associated_products_details[] = array(
                                'id' => $associated_product->getId(),
                                'sku' => $associated_product->getSku()
                                );
                                $associated_price = $this->_productFactory->create()->load($associated_product->getId())->getPrice();
                                $associated_product2 = $this->_productFactory->create()->load($associated_product->getId());
                                $related_special_price = $associated_product2->getSpecialPrice();
                                $associated_products_list[] = array(
                                'color' => $associated_product->getColor(),
                                'id' => $associated_product->getId(),
                                'sku' => $associated_product->getSku(),
                                'name' => $this->_productModel->load($associated_product->getId())->getName(),
                                'image' => $this->_imageHelper->init($associated_product2, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                                'status' => $status,
                                'qty' => $qty,
                                'price' => $this->_cartHelper->getPriceByStoreWithCurrency($finalPrice, $storeId),
                                'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($related_special_price, $storeId),
                                'save_discount' => $this->_cartHelper->getDiscount($associated_product->getId()),
                                );
                                $prices[] = $associated_product->getPrice();
                            }
                        }
                    }
                    $wishlist_detail = $this->checkwishlist($productId);
                    $byiUrl = '';
                    $isByiExists = '';
                    
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $amount = $product->getPrice();
                    if ($product->getTypeId() == 'configurable') {
                        if (!empty($prices)) {
                            $amount = min($prices);
                        }
                    }
                    $shortDescription = $this->_helperOutput->productAttribute($product, nl2br($product->getDescription()), 'description');
                    $product_details[] = array(
                        'id' => $product->getId(),
                        'sku' => $product->getSku(),
                        'name' => $product->getName(),
                        'type' => $product->getTypeId(),
                        'status' => $status,
                        'in_stock' => $isInStock,
                        'weight' => $product->getWeight(),
                        'qty' => (string)$pro_qty,
                        'price' => $this->_cartHelper->getPriceByStoreWithCurrency($mainfinalPrice, $storeId),
                        'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($product->getSpecialPrice(), $storeId),
                        'save_discount' => $this->_cartHelper->getDiscount($product->getId()),
                        'description' => $this->_helperOutput->productAttribute($product, nl2br($product->getDescription()), 'description'),
                        'short_description' => $this->_helperOutput->productAttribute($product, nl2br($product->getShortDescription()), 'short_description'),
                        'is_wishlisted' => $wishlist_detail['in_wishlist'],
                        'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                        'review_count' => $summaryData['review_count'],
                        'average_rating' => $summaryData['rating_summary'],
                        'image' => $this->_imageHelper->init($product, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                        'associated_skus' => $associated_products_details,
                        'all_images' => $images,
                        'product_url' => $product->getProductUrl(),
                        'byi_url_id' => $isByiExists,
                        'byi_url' => $byiUrl,
                    );

                    // 
                    $customObject = $this->_objectFactory->create();
                    $customObject->setProductData($product_details);
                    $this->_eventManager->dispatch('productdetail_response_before', ['productDetailData' => $customObject]);
                    $product_details = $customObject->getProductData();

                    $config_data = $this->getProductConfig($product->getId(), $associated_products, $storeId);
                    $productResultArray = array('productdata' => $product_details, 'associated_products_list' => $associated_products_list, 'config_data' => $config_data, 'related_products_list' => $related_products_list, 'form_key' => $this->formKey->getFormKey());
                    $jsonResult->setData($productResultArray);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $productResultArr = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
                $jsonResult->setData($productResultArr);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    protected function getProductConfig($pid, $associated_products, $storeId)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
            $product_id = $pid;            
            $product = $this->_productFactory->create()->setStoreId($storeId)->load($product_id);
            $this->_frameworkRegistry->register('product', $product);
            $config_data = array();
            $linkids = array();
            if ($product->isSaleable()) {
                $status = 1;
            } else {
                $status = 0;
            }
            if ($status == 1) {
                if ($product->getTypeId() == 'grouped') {
                    $associated_products = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    foreach ($associated_products as $_item) {
                        $config_data[] = array(
                            'name' => $_item->getName(),
                            'price' => $this->_cartHelper->getPriceByStoreWithCurrency($_item->getPrice(), $storeId),
                            'is_in_stock' => ($product->isSaleable()) ? 1 : 0,
                            'product_id' => $_item->getId()
                        );
                    }
                } elseif ($product->getTypeId() == 'simple' || $product->getTypeId() == 'virtual') {
                    foreach ($product->getOptions() as $o) {
                        $optionType = $o->getType();
                        $values = $o->getValues();

                        $config_data[$o->getId()]['title'] = $o->getTitle();
                        if ($o->getTitle() == "Choose Product Color") {
                            $config_data[$o->getId()]['type'] = 'color';
                        } else {
                            $config_data[$o->getId()]['type'] = $optionType;
                        }
                        $config_data[$o->getId()]['is_required'] = $o->getIsRequire();
                        $config_data[$o->getId()]['order'] = $o->getSortOrder();
                        $temp_config_data = array();
                        if (count($values) > 0) {
                            foreach ($values as $k => $v) {
                                $temp_config_data[] = array(
                                    'option_type_id' => $v->getOptionTypeId(),
                                    'option_id' => $v->getOptionId(),
                                    'sku' => $v->getData('sku'),
                                    'default_title' => $v->getData('default_title'),
                                    'title' => $v->getData('title'),
                                    'default_price' => $this->_cartHelper->getPriceByStoreWithCurrency($v->getData('default_price'), $storeId),
                                    'default_price_type' => $v->getData('default_price_type'),
                                    'price' => $this->_cartHelper->getPriceByStoreWithCurrency($v->getData('price'), $storeId),
                                    'price_type' => $v->getData('price_type'),
                                );
                            }
                        } else {
                            $temp_config_data[] = array(
                                'option_type_id' => $o->getOptionTypeId(),
                                    'option_id' => $o->getOptionId(),
                                    'sku' => $o->getData('sku'),
                                    'default_title' => $o->getData('default_title'),
                                    'title' => $o->getData('title'),
                                    'default_price' => $this->_cartHelper->getPriceByStoreWithCurrency($o->getData('default_price'), $storeId),
                                    'default_price_type' => $o->getData('default_price_type'),
                                    'price' => $this->_cartHelper->getPriceByStoreWithCurrency($o->getData('price'), $storeId),
                                    'price_type' => $o->getData('price_type'),
                                'type' => $optionType
                            );
                        }
                        $config_data[$o->getId()]['attributes'] = $temp_config_data;
                        unset($temp_config_data);
                    }
                } elseif ($product->getTypeId() == 'downloadable') {
                    $_myLinksCollection = $this->_downloadableFactory->create();
                    $_myLinksCollection->addProductToFilter($product->getId());
                    $i = 0;
                    $linkids = array();
                    if (sizeof($_myLinksCollection) > 0) {
                        $title = $product->getLinksTitle();
                        if (!isset($title)) {
                            $title = "";
                        }
                        $linkids['title'] = $title;
                        $linkids['links_purchased_separately'] = $product->getLinksPurchasedSeparately();
                        foreach ($_myLinksCollection as $_link) {
                            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                            $mediaUrl = $mediaUrl . 'downloadable/files/links';
                            $mediaUrlSample = $mediaUrl . 'downloadable/files/link_samples';
                            $sample = "";
                            if ($_link->getSample_type() == 'url') {
                                $sample = $_link->getSample_url();
                                if (!isset($sample)) {
                                    $sample = "";
                                }
                            } elseif ($_link->getSample_type() == 'file') {
                                $file = $_link->getSample_file();
                                if (isset($file)) {
                                    $sample = $mediaUrlSample . $_link->getSample_file();
                                } else {
                                    $sample = "";
                                }
                            }
                            $price = $_link->getPrice();
                            $title = $_link->getStore_title();
                            if (!isset($title)) {
                                $title = "";
                            }
                            $linkidsCollection [] = array('id' => $_link->getId(), 'store_title' => "Sample# ".$i, 'is_shareable' => $_link->getIs_shareable(), 'number_of_downloads' => $_link->getNumber_of_downloads(), 'price' => $price, 'sample' => $sample);
                            $i++;
                        }
                    }
                } elseif ($product->getTypeId() == 'configurable') {
                    $config_object = $objectManager->get('Magento\Framework\View\Element\BlockFactory')->createBlock('Magento\ConfigurableProduct\Block\Product\View\Type\Configurable');
                    $config_data = json_decode($config_object->getJsonConfig(), true);
                    $configArray = json_decode($config_object->getJsonConfig(), true);
                    $attributeKey = array();

                    foreach ($configArray['attributes'] as $attrKey => $attrArray) {
                        $attributeKey[] = $attrKey;
                        
                        if ($attrArray['code'] == 'color') {
                            $colorOptions = $attrArray['options'];
                            foreach ($colorOptions as $key => $colorcode) {
                                $colorlabel = $colorcode['label'];
                                $colorid = $colorcode['id'];
                                $hashcodeData = $this->_swatchHelper->getSwatchesByOptionsId([$colorid]);
                                $url = $hashcodeData[$colorid]['value'];
                                $config_data['attributes'][$attrKey]['options'][$key]['image'] = $url;
                                unset($url);
                            }
                        }
                    }
                    $config_data['attribute_keys'] = $attributeKey;
                }
            }
            if (isset($linkidsCollection)) {
                $linkids['link_details'] = $linkidsCollection;
            }
            if ($status) {
                $productResultArr = array('status' => $status, 'type' => $product->getTypeId(), 'config' => $config_data, 'links' => $linkids);
            } else {
                $productResultArr = array('status' => $status, 'error' => 'Product is not saleable.', 'type' => $product->getTypeId(), 'config' => $config_data, 'links' => $linkids);
            }
            return $productResultArr;
        } catch (\Exception $e) {
            $productResultArr = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function checkwishlist($productID)
    {
        $wishlistArray = array('in_wishlist' => false, 'wishlist_item_id' => null);
        if ($this->_wishlistHelper->isAllow()) {
            foreach ($this->_wishlistHelper->getWishlistItemCollection() as $wishlistItem) {
                if ($productID == $wishlistItem->getProduct()->getId()) {
                    $wishlistArray = array('in_wishlist' => true, 'wishlist_item_id' => $wishlistItem->getId());
                    break;
                }
            }
        }
        return $wishlistArray;
    }
    protected function getProductRatingSummary($storeId, $productId)
    {
        $reviewSummary = array('review_count' => null, 'rating_summary' => null);
        $model = $this->_reviewSummaryModel->setStoreId($storeId)->load($productId);
        $reviewSummary = array('review_count' => $model->getReviewsCount(), 'rating_summary' => $model->getRatingSummary());
        return $reviewSummary;
    }
    private function getSalebleQty($productSku)
    {
        $code = 'base';
        $type = 'website';
        $finalSalesData = array();
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $salesChannelId = $objectManager->get('Magento\InventorySales\Model\ResourceModel\StockIdResolver')->resolve($type, $code);
            $stockName = $objectManager->get('Magento\Inventory\Model\Stock')->load($salesChannelId);
            $stockName = $stockName->getName();
            $allowProduct = $objectManager->get('Magento\InventorySales\Model\IsProductSalableCondition\IsProductSalableConditionChain')->execute($productSku, $salesChannelId);
            $this->_salebleModel = $objectManager->get('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
            $salebleModelData = $this->_salebleModel->execute((string)$productSku);
            foreach ($salebleModelData as $key => $value) {
                if ($value['stock_name'] == $stockName) {
                    $finalSalesData['stock_name'] = $value['stock_name'];
                    $finalSalesData['qty'] = $value['qty'];
                    $finalSalesData['manage_stock'] = $value['manage_stock'];
                }
            }
            return $finalSalesData;
        } catch (\Exception $e) {
            $salebleModelData = array();
            return $salebleModelData;
        }
        return $salebleModelData;
    }
}
