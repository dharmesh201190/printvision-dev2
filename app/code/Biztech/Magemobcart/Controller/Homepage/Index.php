<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_COLOR = 'magemobcart/themeselection/primary_background';
    const XML_PATH_SECONDARYCOLOR = 'magemobcart/themeselection/secondary_background';
    const XML_PATH_BESTSELLER = 'magemobcart/magemobcart_general/enable_bestsellerblock';
    const XML_PATH_OUTOFSTOCK = 'cataloginventory/options/show_out_of_stock';
    const XML_PATH_NEWARRIVAL = 'magemobcart/magemobcart_general/enable_newarrivals';
    const XML_PATH_NEWPRODUCT = 'magemobcart/magemobcart_general/display_newproduct_as';
    const XML_PATH_CATEGORY = 'magemobcart/magemobcart_general/display_category';

    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $productModel;
    protected $cartHelper;
    protected $stockItemRepository;
    protected $imageHelper;
    protected $wishlistHelper;
    protected $reviewSummaryModel;
    protected $customerSession;
    protected $bannersliderModel;
    protected $offersliderModel;
    protected $magemobcartModel;
    protected $cartModel;
    protected $scopeConfig;
    protected $catalogruleModel;
    protected $stockInterface;
    protected $productFactory;
    protected $notificationHistoryModel;
    protected $notificationHelper;
    protected $localeDate;
    protected $formKey;
    protected $_eventManager;
    protected $_objectFactory;

    /**
     * @param Context                                                   $context
     * @param JsonFactory                                               $jsonFactory
     * @param Http                                                      $request
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Catalog\Model\Product                            $productModel
     * @param \Magento\Catalog\Model\ProductFactory                     $productFactory
     * @param \Biztech\Magemobcart\Helper\Data                          $cartHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Catalog\Helper\Image                             $imageHelper
     * @param \Magento\Wishlist\Helper\Data                             $wishlistHelper
     * @param \Magento\Review\Model\Review\Summary                      $reviewSummaryModel
     * @param \Magento\Customer\Model\Session                           $customerSession
     * @param \Biztech\Magemobcart\Model\Bannerslider                   $bannersliderModel
     * @param \Biztech\Magemobcart\Model\Offerslider                    $offersliderModel
     * @param \Biztech\Magemobcart\Model\Magemobcart                    $magemobcartModel
     * @param \Magento\Checkout\Model\Cart                              $cartModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig
     * @param \Magento\CatalogRule\Model\Rule                           $catalogruleModel
     * @param \Magento\CatalogInventory\Api\StockStateInterface         $stockInterface
     * @param \Biztech\Magemobcart\Model\Notificationhistory            $notificationHistoryModel
     * @param \Biztech\Magemobcart\Helper\Notification                  $notificationHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface      $localeDate
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
        \Magento\Customer\Model\Session $customerSession,
        \Biztech\Magemobcart\Model\Bannerslider $bannersliderModel,
        \Biztech\Magemobcart\Model\Offerslider $offersliderModel,
        \Biztech\Magemobcart\Model\Magemobcart $magemobcartModel,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogRule\Model\Rule $catalogruleModel,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel,
        \Biztech\Magemobcart\Helper\Notification $notificationHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_productModel = $productModel;
        $this->_cartHelper = $cartHelper;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_imageHelper = $imageHelper;
        $this->_wishlistHelper = $wishlistHelper;
        $this->_reviewSummaryModel = $reviewSummaryModel;
        $this->_customerSession = $customerSession;
        $this->_bannersliderModel = $bannersliderModel;
        $this->_offersliderModel = $offersliderModel;
        $this->_magemobcartModel = $magemobcartModel;
        $this->_cartModel = $cartModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_catalogruleModel = $catalogruleModel;
        $this->_stockInterface = $stockInterface;
        $this->_productFactory = $productFactory;
        $this->_notificationHistoryModel = $notificationHistoryModel;
        $this->_notificationHelper = $notificationHelper;
        $this->_localeDate = $localeDate;
        $this->formKey = $formKey;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get application landing page
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
            try {
                $dashboardResponse = array();
                $postData = $this->_request->getParams();
                $sessionId = '';
                if (isset($postData['session_id']) && $postData['session_id'] != null) {
                    $sessionId = $postData['session_id'];
                    if (!$this->_customerSession->isLoggedIn()) {
                        $customerId = explode("_", $sessionId);
                        $this->_cartHelper->relogin($customerId[0]);
                    }
                    if ($this->_customerSession->isLoggedIn()) {
                        $customerId = explode("_", $sessionId);
                    }
                }
                $storeId = $postData['storeid'];
                if ($storeId == "") {
                    $storeId = $this->_storeManager->getStore()->getId();
                }
                $storeIds = $storeId.",0";
                $storeIds = explode(",", $storeIds);
                $banners = $this->_bannersliderModel->getCollection()
                ->addFieldToFilter(
                    'store_id',
                    array(
                        array('finset'=> array('0')),
                        array('finset'=> array($storeId)),
                    )
                )
                ->addFieldToFilter('status', 1)->setOrder('sort_order', 'ASC')->getData();
                $offers = $this->_offersliderModel->getCollection()
                ->addFieldToFilter(
                    'store_id',
                    array(
                        array('finset'=> array('0')),
                        array('finset'=> array($storeId)),
                    )
                )
                ->addFieldToFilter('status', 1)->setOrder('sort_order', 'ASC')->getData();
                
                $blockCollection = $this->_magemobcartModel->getCollection()
                ->addFieldToFilter(
                    'store_id',
                    array(
                        array('finset'=> array('0')),
                        array('finset'=> array($storeId)),
                    )
                )
                ->setOrder('sort_order', 'ASC')->addFieldToFilter('status', 1);
                $blockCollection->getSelect()->limit(5);

                $featuredBlocks = $blockCollection->getData();
                $count = 0;
                foreach ($featuredBlocks as $value) {
                    if ($value['is_showing'] == 1) {
                        $featuredBlocks[$count]['is_showing'] = true;
                    } else {
                        $featuredBlocks[$count]['is_showing'] = false;
                    }
                    $count++;
                }
                $bestsellerProducts = $this->getBestSellerProducts($storeId);
                
                $newProducts = $this->getAllNewProducts($storeId);
                if (empty($newProducts)) {
                    $newProducts = array("item_count" => null,"productCollection" => null);
                }
                $dashboardResponse['banners'] = $banners;
                $dashboardResponse['offers'] = $offers;
                $dashboardResponse['best_seller'] = $bestsellerProducts;
                $dashboardResponse['new_products'] = $newProducts;
                $dashboardResponse['featured_category_block'] = $featuredBlocks;

                $themePrimarycolor = $this->_scopeConfig->getValue(self::XML_PATH_COLOR);
                $dashboardResponse['color'] = $themePrimarycolor;
                $themeSecondarycolor = $this->_scopeConfig->getValue(self::XML_PATH_SECONDARYCOLOR);
                $dashboardResponse['secondary_color'] = $themeSecondarycolor;

                if (isset($postData['customer_id']) && $postData['customer_id'] != "") {
                    $customerId = $postData['customer_id'];
                    $dashboardResponse['notification_count'] = (bool)$this->_notificationHelper->getNotificationCount($customerId);
                }

                /*****************Customer cart data *******************/

                $count = $this->_cartModel->getQuote()->getItemsCount();
                if (!isset($count)) {
                    $count = "0";
                } else {
                    $count = $this->_cartModel->getQuote()->getItemsCount();
                }
                $dashboardResponse['cart_count'] = $count;


                /*****************Customer cart data *******************/
                
                $dashboardResponse['magento_version'] = false;
                $jsonResult->setData($dashboardResponse);
                return $jsonResult;
                $session = $this->_customerSession;
            } catch (\Exception $e) {
                $dashboardResponse = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($dashboardResponse);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    /**
     * This function is used for get best seller product list
     * @param  int $storeId
     * @param  int $seeall
     * @param  int $page
     * @param  int $limit
     * @return Array
     */
    public function getBestSellerProducts($storeId, $seeall = null, $page = null, $limit = null)
    {
        $jsonResult = $this->_jsonFactory->create();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
        $enableDisplayBestSellerProduct = $this->_scopeConfig->getValue(self::XML_PATH_BESTSELLER);
        if ($enableDisplayBestSellerProduct == 0) {
            $responseArr['productCollection'] = "";
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $product_list = array();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productCollection = $objectManager->create('Magento\Reports\Model\ResourceModel\Report\Collection\Factory');
            $bestSelProducts = $productCollection->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
            $bestSelProducts->setPeriod('month');
            $out_of_stock = $this->_scopeConfig->getValue(self::XML_PATH_OUTOFSTOCK);
            if ($seeall != true) {
                $bestSelProducts->getSelect()->limit(5, 0);
            }
            $total_collection = count($bestSelProducts);

            $bestSelProducts->setCurPage($page);

            if ($limit) {
                $bestSelProducts->setPageSize($limit);
            }
            foreach ($bestSelProducts as $product) {
                $configurableTypeIds = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->getParentIdsByChild($product['product_id']);
                $parentId = array_shift($configurableTypeIds);
                if (isset($parentId)) {
                    $productFinalId = $parentId;
                } else {
                    $productFinalId = $product['product_id'];
                }
                $product_data = $this->_productFactory->create()->setStoreId($storeId)->load($product['product_id']);
                $productSku = (string)$product_data->getSku();
                $allow = $this->allowProduct($productSku);
                if ($allow == "") {
                    continue;
                }
                if ($product_data->getTypeId() == 'simple' || $product_data->getTypeId() == 'virtual') {
                    if ($product_data->getData('has_options')) {
                        $hasOptions = "1";
                    } else {
                        $hasOptions = "0";
                    }
                } elseif ($product_data->getTypeId() == 'configurable' || $product_data->getTypeId() == 'grouped' || $product_data->getTypeId() == 'bundle') {
                    $hasOptions = "1";
                } elseif ($product_data->getTypeId() == 'downloadable') {
                    $hasOptions = $product_data->getData('links_purchased_separately');
                }
                $status = $product_data->getStatus();
                $stockState = $this->_stockInterface;
                
                
                $qty = $stockState->getStockQty($product_data->getId(), $product_data->getStore()->getWebsiteId());
                $summaryData = $this->getProductRatingSummary($storeId, $product_data->getId());
                $wishlist_detail = $this->checkwishlist($product->getId());
                
                $stock_item = $this->_stockItemRepository->get($product_data->getId());
                if ($stock_item->getBackorders() != 0) {
                    if ($stock_item->getIsInStock() == 0) {
                        $isInStock = '0';
                    } else {
                        $isInStock = '1';
                    }
                } else {
                    if ($qty < 0 || $stock_item->getIsInStock() == 0) {
                        $qty = 'Out of Stock';
                        $isInStock = '0';
                    } else {
                        $isInStock = '1';
                    }
                }
                if ($this->_catalogruleModel->calcProductPriceRule($product_data, $product_data->getPrice())) {
                    $specialPrice = $this->_catalogruleModel->calcProductPriceRule($product_data, $product_data->getPrice());
                } else {
                    $specialPrice = $product_data->getSpecialPrice();
                }
                $associated_products = array();
                if ($product_data->getTypeId() == 'grouped') {
                    $associated_products = $product_data->getTypeInstance(true)->getAssociatedProducts($product_data);
                } elseif ($product_data->getTypeId() == 'configurable') {
                    $associated_products = $product_data->getTypeInstance()->getUsedProducts($product_data);
                } elseif ($product_data->getTypeId() == 'bundle') {
                    $associated_products = $product_data->getTypeInstance(true)->getSelectionsCollection($product_data->getTypeInstance(true)->getOptionsIds($product_data), $product_data);
                }
                $prices = array();
                $associated_products_details = array();
                $associated_products_list = array();
                $related_products_list = array();

                foreach ($associated_products as $associated_product) {
                    $associated_productStockData = $this->_stockItemRepository->get($associated_product->getId());
                    $qty = $associated_productStockData->getQty();

                    if ($qty < 0 || $associated_productStockData->getIsInStock() == 0) {
                        $qty = 'Out of Stock';
                    } else {
                        $associated_products_details[] = array(
                            'id' => $associated_product->getId(),
                            'sku' => $associated_product->getSku()
                        );
                        $associated_price = $this->_productFactory->create()->load($associated_product->getId())->getPrice();
                        $associated_products_list[] = array(
                            'color' => $associated_product->getColor(),
                            'id' => $associated_product->getId(),
                            'sku' => $associated_product->getSku(),
                            'name' => $this->_productModel->load($associated_product->getId())->getName(),
                            'image' => $this->_imageHelper->init($associated_product, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                            'status' => $status,
                            'qty' => $qty,
                            'price' => $this->_cartHelper->getPriceByStoreWithCurrency($associated_price, $storeId),
                        );
                        $prices[] = $associated_product->getPrice();
                    }
                }
                $amount = $product_data->getPrice();
                if ($product_data->getTypeId() == 'configurable') {
                    $amount = min($prices);
                }
                $product_list[] = array(
                    'id' => $productFinalId,
                    'sku' => $product_data->getSku(),
                    'name' => $product_data->getName(),
                    'status' => $status,
                    'qty' => $qty,
                    'in_stock' => $stock_item->getIsInStock(),
                    'price' => $this->_cartHelper->getPriceByStoreWithCurrency($amount, $storeId),
                    'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($specialPrice, $storeId),
                    'image' => $this->_imageHelper->init($product_data, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                    'type' => $product_data->getTypeId(),
                    'is_wishlisted' => $wishlist_detail['in_wishlist'],
                    'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                    'review_count' => $summaryData['review_count'],
                    'average_rating' => $summaryData['rating_summary'],
                    'has_options' => $hasOptions,
                    'save_discount' => $this->_cartHelper->getDiscount($product_data->getId()),
                    'byi_url_id' => '',
                    'byi_url' => ''
                );
            }
            
            $customObject = $this->_objectFactory->create();
            $customObject->setProductData($product_list);
            $this->_eventManager->dispatch('productdetail_response_before', ['productDetailData' => $customObject]);
            $product_list = $customObject->getProductData();
            
            $responseArr['item_count'] = $total_collection;
            $responseArr['productCollection'] = $product_list;
            $responseArr['current_page'] = $page;
            if (isset($limit)) {
                if ($page + 1 <= ceil($total_collection / $limit)) {
                    $responseArr['next_page'] = $page + 1;
                }
            }
            return $responseArr;
        }
    }

    /**
     * This function is used for get new product list
     * @param  int $storeId
     * @param  int $seeall
     * @param  int $page
     * @param  int $limit
     * @return Array
     */
    public function getAllNewProducts($storeId, $seeall = null, $page = null, $limit = null)
    {
        $jsonResult = $this->_jsonFactory->create();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
        $enableDisplayBestSellerProduct = $this->_scopeConfig->getValue(self::XML_PATH_NEWARRIVAL);
        if ($enableDisplayBestSellerProduct == 0) {
            $responseArr['productCollection'] = "";
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $displayProduct = $this->_scopeConfig->getValue(self::XML_PATH_NEWPRODUCT);
            $todayDate = date('Y-m-d');
            if ($displayProduct == '1') {
                $products = $this->_productModel->getCollection()
                ->setStoreId($storeId)
                ->addAttributeToFilter('status', array("eq" => 1))
                ->addAttributeToSort('news_from_date', 'desc')
                ->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('visibility', array("neq" => 1));
                $products->addAttributeToFilter(
                    'news_from_date',
                    [
                        'or' => [
                            0 => ['date' => true, 'to' => $todayEndOfDayDate],
                            1 => ['is' => new \Zend_Db_Expr('null')],
                        ]
                    ]
                )->addAttributeToFilter(
                    [
                        ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                    ]
                );
            }
            $product_list = array();
            if ($displayProduct == '2') {
                $displayType = $this->_scopeConfig->getValue(self::XML_PATH_CATEGORY);
                $products = $this->_productModel->getCollection()->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', array("eq" => 1))
                ->addAttributeToFilter('visibility', array("neq" => 1))
                ->addCategoriesFilter(array('in' => $displayType));
                $products->getSelect()->group('e.entity_id');
                $products->setOrder('entity_id', 'ASC');
                $products->addAttributeToFilter(
                    'news_from_date',
                    [
                        'or' => [
                            0 => ['date' => true, 'to' => $todayStartOfDayDate],
                            1 => ['is' => new \Zend_Db_Expr('null')],
                        ]
                    ]
                )->addAttributeToFilter(
                    [
                        ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                    ]
                );
            }
            if ($seeall != true) {
                $products->getSelect()->limit(5, 0);
            }
            $total_collection = count($products);

            $products->setCurPage($page);

            if ($limit) {
                $products->setPageSize($limit);
            }
            foreach ($products as $product) {
                $product_data = $this->_productFactory->create()->setStoreId($storeId)->load($product['entity_id']);
                if ($product_data->getTypeId() == 'simple' || $product_data->getTypeId() == 'virtual') {
                    if ($product_data->getData('has_options')) {
                        $hasOptions = "1";
                    } else {
                        $hasOptions = "0";
                    }
                } elseif ($product_data->getTypeId() == 'configurable' || $product_data->getTypeId() == 'grouped' || $product_data->getTypeId() == 'bundle') {
                    $hasOptions = "1";
                } elseif ($product_data->getTypeId() == 'downloadable') {
                    $hasOptions = $product_data->getData('links_purchased_separately');
                }
                /*****************************Product type wise price*********************************/
                $finalPrice = $product_data->getPrice();
                $associated_products = array();
                if ($product_data->getTypeId() == 'grouped') {
                    $associated_products = $product_data->getTypeInstance(true)->getAssociatedProducts($product_data);
                } elseif ($product_data->getTypeId() == 'configurable') {
                    $associated_products = $product_data->getTypeInstance()->getUsedProducts($product_data);
                }
                $prices = array();
                foreach ($associated_products as $associated_product1) {
                    $priceAssociateProduct = $this->_productFactory->create()->setStoreId($storeId)->load($associated_product1->getId());
                    $prices[] = $priceAssociateProduct->getFinalPrice();
                }
                if (!empty($prices)) {
                    $finalPrice = min($prices);
                }


                /*************************************************************************************/
                $status = $product_data->getStatus();

                $stockState = $this->_stockItemRepository->get($product_data->getId(), $product_data->getStore()->getWebsiteId());
                $qty = $stockState->getQty();
                $summaryData = $this->getProductRatingSummary($storeId, $product_data->getId());
                $wishlist_detail = $this->checkwishlist($product->getId());
                if ($qty == 0 && $stockState->getIsInStock() == 0 && $product_data->getTypeId() == "simple") {
                    $qty = 'Out of Stock';
                }
                if ($this->_catalogruleModel->calcProductPriceRule($product_data, $product_data->getPrice())) {
                    $specialPrice = $this->_catalogruleModel->calcProductPriceRule($product_data, $product_data->getPrice());
                } else {
                    $specialPrice = $product_data->getSpecialPrice();
                }
                $product_list[] = array(
                    'id' => $product_data->getId(),
                    'sku' => $product_data->getSku(),
                    'name' => $product_data->getName(),
                    'status' => $status,
                    'qty' => $qty,
                    'in_stock' => $stockState->getIsInStock(),
                    'price' => $this->_cartHelper->getPriceByStoreWithCurrency($finalPrice, $storeId),
                    'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($specialPrice, $storeId),
                    'image' => $this->_imageHelper->init($product_data, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                    'type' => $product_data->getTypeId(),
                    'is_wishlisted' => $wishlist_detail['in_wishlist'],
                    'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                    'review_count' => $summaryData['review_count'],
                    'average_rating' => $summaryData['rating_summary'],
                    'save_discount' => $this->_cartHelper->getDiscount($product_data->getId()),
                    'byi_url_id' => '',
                    'byi_url' => ''
                );
            }
            // echo "<pre>"; print_r($product_list); exit;
            $customObject = $this->_objectFactory->create();
            $customObject->setProductData($product_list);
            $this->_eventManager->dispatch('productdetail_response_before', ['productDetailData' => $customObject]);
            $product_list = $customObject->getProductData();

            $responseArr['item_count'] = $total_collection;
            $responseArr['productCollection'] = $product_list;
            $responseArr['current_page'] = $page;
            if (isset($limit)) {
                if ($page + 1 <= ceil($total_collection / $limit)) {
                    $responseArr['next_page'] = $page + 1;
                }
            }
            return $responseArr;
        }
    }
    /**
     * This function is used for get the particular product rating summary
     * @param  int $storeId
     * @param  int $productId
     * @return Array
     */
    protected function getProductRatingSummary($storeId, $productId)
    {
        $reviewSummaryArray = array('review_count' => null, 'rating_summary' => null);
        $reviewModelData = $this->_reviewSummaryModel->setStoreId($storeId)->load($productId);
        $reviewSummaryArray = array('review_count' => $reviewModelData->getReviewsCount(), 'rating_summary' => $reviewModelData->getRatingSummary());
        return $reviewSummaryArray;
    }

    /**
     * This function is used for the get product is in wishlist or not
     * @param  int $productId
     * @return Array
     */
    protected function checkwishlist($productId)
    {
        $wishlistArray = array('in_wishlist' => false, 'wishlist_item_id' => null);
        if ($this->_wishlistHelper->isAllow()) {
            foreach ($this->_wishlistHelper->getWishlistItemCollection() as $wishlistItem) {
                if ($productId == $wishlistItem->getProduct()->getId()) {
                    $wishlistArray = array('in_wishlist' => true, 'wishlist_item_id' => $wishlistItem->getId());
                    break;
                }
            }
        }
        return $wishlistArray;
    }
    private function getSalebleQty($productSku)
    {
        $code = 'base';
        $type = 'website';
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
    private function allowProduct($productSku)
    {
        $code = 'base';
        $type = 'website';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $salesChannelId = $objectManager->get('Magento\InventorySales\Model\ResourceModel\StockIdResolver')->resolve($type, $code);
        $allowProduct = $objectManager->get('Magento\InventorySales\Model\IsProductSalableCondition\IsProductSalableConditionChain')->execute($productSku, $salesChannelId);
        return $allowProduct;
    }
}
