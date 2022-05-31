<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Getbestsellers extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_BESTSELLER = 'magemobcart/magemobcart_general/enable_bestsellerblock';
    const XML_SHOW_OUTOFSTOCK = 'cataloginventory/options/show_out_of_stock';
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
    protected $stockItemRepository;
    protected $formKey;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Magento\Catalog\Model\Product $productModel,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Review\Model\Review\Summary $reviewSummaryModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogRule\Model\Rule $catalogruleModel,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
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
        $this->_stockItemRepository = $stockItemRepository;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all best seller products list
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
                $bestSeller = $this->getBestSellerProducts($storeId, $seeAll, $page, $limit);
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
    public function getBestSellerProducts($storeId, $seeall = null, $page = null, $limit = null)
    {
        $jsonResult = $this->_jsonFactory->create();
        $enableDisplayBestSellerProduct = $this->_scopeConfig->getValue(self::XML_PATH_BESTSELLER);
        if ($enableDisplayBestSellerProduct == 0) {
            $responseArr['productCollection'] = "";
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productCollection = $objectManager->create('Magento\Reports\Model\ResourceModel\Report\Collection\Factory');
            $bestSelProducts = $productCollection->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
            $bestSelProducts->setPeriod('month');
            $out_of_stock = $this->_scopeConfig->getValue(self::XML_SHOW_OUTOFSTOCK);
            
            if ($seeall != true) {
                $bestSelProducts->getSelect()->limit(5, 0);
            }
            $total_collection = count($bestSelProducts);

            $bestSelProducts->setCurPage($page);

            if ($limit) {
                $bestSelProducts->setPageSize($limit);
            }
            foreach ($bestSelProducts as $product) {
                $product_data = $this->_productModel->setStoreId($storeId)->load($product['product_id']);
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
                        $in_stock = '0';
                    } else {
                        $in_stock = '1';
                    }
                } else {
                    if ($stock_item->getManageStock() == 0) {
                        if ($stock_item->getIsInStock() == true) {
                            $in_stock = '1';
                        } else {
                            $in_stock = '0';
                        }
                    } else {
                        $in_stock = (int)$stock_item->getIsInStock();
                    }
                }
                if ($qty < 0 || $stock_item->getIsInStock() == 0) {
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
                'in_stock' => $in_stock,
                'price' => $this->_cartHelper->getPriceByStoreWithCurrency($product_data->getPrice(), $storeId),
                'special_price' => $this->_cartHelper->getPriceByStoreWithCurrency($specialPrice, $storeId),
                'image' => $this->_imageHelper->init($product_data, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                'type' => $product_data->getTypeId(),
                'is_wishlisted' => $wishlist_detail['in_wishlist'],
                'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                'review_count' => $summaryData['review_count'],
                'average_rating' => $summaryData['rating_summary'],
                'has_options' => $hasOptions,
                'save_discount' => $this->_cartHelper->getDiscount($product_data->getId()),
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewModelData = array();
        $reviewCount = "";
        $ratingSummary = "";
        $reviewModelData = $objectManager->create('Magento\Review\Model\Review\Summary')->setStoreId($storeId)->load($productId);
        $reviewCount = $reviewModelData->getReviewsCount();
        $ratingSummary = $reviewModelData->getRatingSummary();

        if (array_key_exists('reviews_count', $reviewModelData->getData())) {
            $reviewSummaryArray = array('review_count' => $reviewCount, 'rating_summary' => $ratingSummary);
        } else {
            $reviewSummaryArray = array('review_count' => null, 'rating_summary' => null);
        }
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
