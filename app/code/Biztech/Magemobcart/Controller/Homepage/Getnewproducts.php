<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Getnewproducts extends \Magento\Framework\App\Action\Action
{
    const XML_SHOW_OUTOFSTOCK = 'cataloginventory/options/show_out_of_stock';
    const XML_PATH_NEWARRIVAL = 'magemobcart/magemobcart_general/enable_newarrivals';
    const XML_PATH_NEWPRODUCT = 'magemobcart/magemobcart_general/display_newproduct_as';
    const XML_PATH_CATEGORY = 'magemobcart/magemobcart_general/display_category';
    protected $jsonFactory;
    protected $request;
    protected $productModel;
    protected $cartHelper;
    protected $imageHelper;
    protected $wishlistHelper;
    protected $reviewSummaryModel;
    protected $customerSession;
    protected $scopeConfig;
    protected $stockInterface;
    protected $catalogruleModel;
    protected $productFactory;
    protected $stockItemRepository;
    protected $localeDate;
    protected $formKey;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Review\Model\Review\Summary $reviewSummaryModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogRule\Model\Rule $catalogruleModel,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_productModel = $productModel;
        $this->_cartHelper = $cartHelper;
        $this->_imageHelper = $imageHelper;
        $this->_wishlistHelper = $wishlistHelper;
        $this->_reviewSummaryModel = $reviewSummaryModel;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_stockInterface = $stockInterface;
        $this->_catalogruleModel = $catalogruleModel;
        $this->_productFactory = $productFactory;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_localeDate = $localeDate;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all New products list
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        $postData = $this->_request->getParams();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            $storeId = $postData['storeid'];
            $seeAll = $postData['see_all'];
            $page = $postData['page'] ? $postData['page'] : 1;
            $limit = $postData['limit'];

            try {
                $bestSeller = $this->getAllNewProducts($storeId, $seeAll, $page, $limit);
                $jsonResult->setData($bestSeller);
                return $jsonResult;
            } catch (\Exception $e) {
                $bestSeller = array(
                'status' => 'false',
                'message' => $e->getMessage()
                );
            }
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
    public function getAllNewProducts($storeId, $seeall = null, $page = null, $limit = null)
    {
        $jsonResult = $this->_jsonFactory->create();
        $enableDisplayBestSellerProduct = $this->_scopeConfig->getValue(self::XML_PATH_NEWARRIVAL);
        if ($enableDisplayBestSellerProduct == 0) {
            $responseArr['productCollection'] = "";
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $todayDate = date('Y-m-d');
            $displayProduct = $this->_scopeConfig->getValue(self::XML_PATH_NEWPRODUCT);
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
                // $products->getSelect()->group('e.entity_id');
                $products->setOrder('entity_id', 'ASC');
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
            if ($seeall != true) {
                $products->getSelect()->limit(5, 0);
            }
            $total_collection = count($products);

            $products->setCurPage($page);

            if ($limit) {
                $products->setPageSize($limit);
            }
            foreach ($products as $product) {
                $productData = $this->_productFactory->create()->setStoreId($storeId)->load($product['entity_id']);
                if ($productData->getTypeId() == 'simple' || $productData->getTypeId() == 'virtual') {
                    if ($productData->getData('has_options')) {
                        $hasOptions = "1";
                    } else {
                        $hasOptions = "0";
                    }
                } elseif ($productData->getTypeId() == 'configurable' || $productData->getTypeId() == 'grouped' || $productData->getTypeId() == 'bundle') {
                    $hasOptions = "1";
                } elseif ($productData->getTypeId() == 'downloadable') {
                    $hasOptions = $productData->getData('links_purchased_separately');
                }
                /*****************************Product type wise price*********************************/
                $finalPrice = $productData->getPrice();
                $associated_products = array();
                if ($productData->getTypeId() == 'grouped') {
                    $associated_products = $productData->getTypeInstance(true)->getAssociatedProducts($productData);
                } elseif ($productData->getTypeId() == 'configurable') {
                    $associated_products = $productData->getTypeInstance()->getUsedProducts($productData);
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
                $status = $productData->getStatus();

                $stockState = $this->_stockItemRepository->get($productData->getId(), $productData->getStore()->getWebsiteId());
                $qty = $stockState->getQty();
                $summaryData = $this->getProductRatingSummary($storeId, $productData->getId());
                $wishlist_detail = $this->checkwishlist($product->getId());
                if ($qty == 0 && $stockState->getIsInStock() == 0 && $productData->getTypeId() == "simple") {
                    $qty = 'Out of Stock';
                }
                if ($this->_catalogruleModel->calcProductPriceRule($productData, $productData->getPrice())) {
                    $specialPrice = $this->_catalogruleModel->calcProductPriceRule($productData, $productData->getPrice());
                } else {
                    $specialPrice = $productData->getSpecialPrice();
                }
                $product_list[] = array(
                'id' => $productData->getId(),
                'sku' => $productData->getSku(),
                'name' => $productData->getName(),
                'status' => $status,
                'qty' => $qty,
                'in_stock' => $stockState->getIsInStock(),
                'price' => $this->_cartHelper->getPriceByStoreWithCurrency($finalPrice, $storeId),
                'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($specialPrice, $storeId),
                'image' => $this->_imageHelper->init($productData, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                'type' => $productData->getTypeId(),
                'is_wishlisted' => $wishlist_detail['in_wishlist'],
                'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                'review_count' => $summaryData['review_count'],
                'average_rating' => $summaryData['rating_summary'],
                'has_options' => $hasOptions,
                // 'save_discount' => $this->_cartHelper->getDiscount($productData->getId()),
                );
            }
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
