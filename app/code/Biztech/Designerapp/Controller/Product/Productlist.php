<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Designerapp\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Productlist extends \Magento\Framework\App\Action\Action
{
    const XML_SHOW_OUTOFSTOCK = 'cataloginventory/options/show_out_of_stock';
    
    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $productModel;
    protected $cartHelper;
    protected $stockItemRepository;
    protected $imageHelper;
    protected $wishlistHelper;
    protected $reviewSummaryModel;
    protected $scopeConfig;
    protected $productFactory;
    protected $stockApiRepository;

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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface      $stockApiRepository
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockApiRepository
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
        $this->_scopeConfig = $scopeConfig;
        $this->_productFactory = $productFactory;
        $this->_stockApiRepository = $stockApiRepository;
        parent::__construct($context);
    }

    /**
     * This function is used for get all categories list with tree structure.
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
            if (array_key_exists("product_name", $postData)) {
                $product_name = $postData['product_name'];
            } else {
                $product_name = "";
            }
            
            
            if (array_key_exists('categoryid', $postData)) {
                $catId = $postData['categoryid'];
            } else {
                $catId = null;
            }
            $storeId = $postData['storeid'];
            $limit = $postData['limit'];
            $page = $postData['page'];
            if (array_key_exists('attributes', $postData)) {
                $attributesFilterBy = json_decode($postData['attributes'], true);
            } else {
                $attributesFilterBy = array();
            }
            $optionIds = array();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            try {
                if (isset($catId) && $catId != "") {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $layerResolver = $objectManager->get(\Magento\Catalog\Model\Layer\Resolver::class);
                    $filterableAttributes = $objectManager->get(\Magento\Catalog\Model\Layer\Category\FilterableAttributeList::class);
                    $filterList = $objectManager->create(
                        \Magento\Catalog\Model\Layer\FilterList::class,
                        [
                        'filterableAttributes' => $filterableAttributes
                        ]
                    );
                    $categoryId = $postData['categoryid'];
                    $categoryLayer = $layerResolver->get()->setCurrentCategory($categoryId);
                    $category = $objectManager->get('Magento\Catalog\Model\Category')->load($categoryId);
                    $filterAttributes = $filterList->getFilters($categoryLayer);
                    $filterArray      = [];

                    $i = 0;
                    foreach ($filterAttributes as $filter) {
                        $attributeLabel = (string) $filter->getName();
                        $attributeCode  = (string) $filter->getRequestVar();
                        $items          = $filter->getItems();
                        $filterValues   = [];

                        $j = 0;
                        foreach ($items as $item) {
                            if ($attributeCode == 'cat') {
                                $filterValues[$j]['label'] = strip_tags($item->getLabel());
                                $filterValues[$j]['value']   = $item->getValue();
                                $filterValues[$j]['count'] = $item->getCount();
                            } elseif ($category->getIsAnchor()) {
                                $filterValues['attribute_id'] = (string) $filter->getRequestVar();
                                $filterValues['attribute_code']  = (string) $filter->getName();
                                $filterValues['options'][$j]['label'] = strip_tags($item->getLabel());
                                $filterValues['options'][$j]['value']   = $item->getValue();
                                $filterValues['options'][$j]['count'] = $item->getCount();
                                $swatchesValues = $category->getSwatches($filter, $item, $j);
                                if (!empty($swatchesValues)) {
                                    $filterValues[$j]['swatch_value'] = $swatchesValues['swatch_value'];
                                    $filterValues[$j]['swatch_type']  = $swatchesValues['swatch_type'];
                                }
                            }
                            $j++;
                        }
                        if ($i != 0) {
                            $filter_attributes[] = $filterValues;
                        }
                        if (!empty($filterValues)) {
                            // $filterArray['attribute_id']  = "test";
                            $filterArray[] = $filterValues;
                        }
                        $attr[$filter->getRequestVar()] = $filter->getName();
                    }
                    $response = array(
                    'attribute_options' => $filterArray,
                    );
                    
                    foreach ($attributesFilterBy as $key => $value) {
                        $optionIds[$value['attribute_code']] = explode(',', $value['option_id']);
                    }
                    $filters = $optionIds;
                    $collection = $category->getProductCollection();
                    $collection->addStoreFilter()->setStoreId($storeId)
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addUrlRewrite()->addAttributeToSelect('*');
                    $collection->addAttributeToFilter('status', array("eq" => 1));
                    $collection->addAttributeToFilter('type_id', array("in" => array('simple', 'grouped', 'configurable', 'virtual', 'downloadable')));
                    $_configChild = array();
                    
                    foreach ($collection as $key => $_product) {
                        if ($_product->getTypeId() == 'configurable') {
                            $_configChild[$key] = $_product->getTypeInstance()->getUsedProductIds($_product);
                        }
                    }

                    $childIds1 = array();
                    foreach ($_configChild as $key => $value) {
                        foreach ($value as $key => $childIds) {
                            $childIds1[] = $childIds;
                        }
                    }
                    $merged_ids = array_merge($collection->getAllIds(), $childIds1);

                    $merged_collection = $this->_productModel->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $merged_ids))
                    ->addAttributeToSelect('*');
                    $merged_collection->addStoreFilter()->setStoreId($storeId)
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addUrlRewrite()->addAttributeToSelect('*');

                    $merged_collection->addAttributeToFilter('type_id', array("in" => array('simple', 'grouped', 'configurable', 'virtual', 'downloadable')));

                    foreach ($filters as $key => $value) {
                        if ($key == "price") {
                            $value = array_map('strval', $value);
                        } else {
                            $value = array_map('intval', $value);
                        }
                        

                        if ($key == 'price') {
                            $priceFilter = array();
                            foreach ($value as $data) {
                                $priceFilter[] = explode('-', $data);
                            }
                            foreach ($priceFilter as $key => $value11) {
                                if ($value11[0] == '') {
                                    $priceFilterArray[$key]['from'] = '0';
                                } else {
                                    $priceFilterArray[$key]['from'] = $value11[0];
                                }
                                if ($value11[1] == '') {
                                    //do nothing
                                } else {
                                    $priceFilterArray[$key]['to'] = $value11[1];
                                }
                            }
                            foreach ($priceFilter as $key => $value1) {
                                $merged_collection->addAttributeToFilter('price', array($priceFilterArray));
                            }
                        } else {
                            $merged_collection->addAttributeToFilter($key, array('in' => $value));
                        }
                    }
                } else {
                    $merged_collection = $this->_productModel->getCollection();
                    if ($product_name != null) {
                        $merged_collection->addStoreFilter()->setStoreId($storeId)
                        ->addMinimalPrice()
                        ->addFinalPrice()
                        ->addUrlRewrite()->addAttributeToSelect('*');
                        $merged_collection->addAttributeToFilter('name', array('like' => '%' . $product_name . '%'));
                    }
                }
                
                $proArray = array();
                foreach ($merged_collection as $product) {
                    if ($catId != "") {
                        if ($product->getvisibility() != "1") {
                            $product_data = $this->_productFactory->create()->setStoreId($storeId)->load($product->getId());
                            $proArray[] = $product_data->getId();
                        } else {
                            $product11 = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
                            $proArray[] = $product11[0];
                        }
                    } else {
                        if ($product->getvisibility() == "1" || $product->getvisibility() == "2") {
                            $product11 = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
                            if (isset($product11[0])) {
                                $proArray[] = $product11[0];
                            }
                        } else {
                            $product_data = $this->_productFactory->create()->setStoreId($storeId)->load($product->getId());
                            $proArray[] = $product_data->getId();
                        }
                    }
                }
                $uniqueArray = array_unique($proArray);
                if (empty($uniqueArray)) {
                    if ($catId) {
                        $result = array('mesage' => 'No such product found.');
                        $jsonResult->setData($result);
                        return $jsonResult;
                    }
                }
                $uniqueArrayCollection = $this->_productModel->getCollection()
                ->setStoreId($storeId)
                ->addFieldToFilter('entity_id', array('in' => $uniqueArray))
                ->addAttributeToSelect('*');
                $merged_collection->addStoreFilter()->setStoreId(1);
                if (array_key_exists('dir', $postData)) {
                    if (array_key_exists('order', $postData)) {
                        $direction = $postData['dir'];
                        $sort_by = $postData['order'];
                        if ($sort_by != null && $direction != null) {
                            $uniqueArrayCollection->setOrder($sort_by, $direction);
                        }
                    }
                }
                
                if ($this->_scopeConfig->getValue('cataloginventory/options/show_out_of_stock') == 0) {
                }
                $i = 0;
                $product_list = array();
                $totalcount = sizeof($uniqueArrayCollection->getData());
                foreach ($uniqueArrayCollection->getData() as $key => $product) {
                    $value = $product['entity_id'];
                    $summaryData = $this->getProductRatingSummary($storeId, $value);
                    $wishlist_detail = $this->checkwishlist($value);
                    $product_data = $this->_productFactory->create()->load($value);
                    $status = $product_data->getStatus();
                    $stock_item = $this->_stockApiRepository->getStockItem($product['entity_id']);
                    $qty = $stock_item->getQty();
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
                    $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
                    if ($product_data->getTypeId() == 'grouped') {
                        $aProductIds = $product_data->getTypeInstance()->getChildrenIds($product_data->getId());
                        $prices = array();

                        foreach ($aProductIds as $ids) {
                            foreach ($ids as $id) {
                                $aProduct = $this->_productFactory->create()->load($id);
                                $prices[] = $aProduct->getFinalPrice();
                            }
                        }
                        krsort($prices);
                        $price_array = array_keys($prices);
                        $price = $this->_cartHelper->getPriceByStoreWithCurrency(min($prices), $storeId);
                    } elseif ($product_data->getTypeId() == 'configurable') {
                        $price = $this->_cartHelper->getPriceByStoreWithCurrency($product_data->getFinalPrice(), $storeId);
                    } else {
                        $price = $this->_cartHelper->getPriceByStoreWithCurrency($product_data->getPrice(), $storeId);
                    }
                    $i++;

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
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $byiUrl = '';
                    $isByiExists = '';
                    
                    $page = $postData['page'];
                    $specialPrice = $this->_cartHelper->getPriceByStoreWithCurrency($product_data->getSpecialPrice(), $storeId);
                    if ($page == 1) {
                        $product_list[] = array(
                        'id' => $product_data->getId(),
                        'sku' => $product_data->getSku(),
                        'name' => $product_data->getName(),
                        'status' => $status,
                        'qty' => $qty,
                        'in_stock' => $in_stock,
                        'price' => $price,
                        'special_price' => $specialPrice,
                        'image' => $this->_imageHelper->init($product_data, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                        'type' => $product_data->getTypeId(),
                        'is_wishlisted' => $wishlist_detail['in_wishlist'],
                        'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                        'review_count' => $summaryData['review_count'],
                        'average_rating' => $summaryData['rating_summary'],
                        'has_options' => $hasOptions,
                        'byi_url_id' => $isByiExists,
                        'byi_url' => $byiUrl,
                        'save_discount' => $this->_cartHelper->getDiscount($product_data->getId()),
                        );
                        if ($i == $limit) {
                            break;
                        }
                    } elseif ($page != 1 && $i >= ((($page - 1) * $limit) + 1)) {
                        $product_list[] = array(
                        'id' => $product_data->getId(),
                        'sku' => $product_data->getSku(),
                        'name' => $product_data->getName(),
                        'status' => $status,
                        'qty' => $qty,
                        'in_stock' => $in_stock,
                        'price' => $price,
                        'special_price' => $specialPrice,
                        'image' => $this->_imageHelper->init($product_data, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                        'type' => $product_data->getTypeId(),
                        'is_wishlisted' => $wishlist_detail['in_wishlist'],
                        'wishlist_item_id' => $wishlist_detail['wishlist_item_id'],
                        'review_count' => $summaryData['review_count'],
                        'average_rating' => $summaryData['rating_summary'],
                        'has_options' => $hasOptions,
                        'byi_url_id' => $isByiExists,
                        'byi_url' => $byiUrl,
                        'save_discount' => $this->_cartHelper->getDiscount($product_data->getId()),
                        );
                        if ($i == (($page - 1) * $limit) + $limit) {
                            break;
                        }
                    }
                }
                if ($page + 1 <= ceil(count($uniqueArray) / $limit)) {
                    $nextCountPage = $page + 1;
                } else {
                    $nextCountPage = "";
                }
                $response = array('productCollection' => $product_list);
                $response['item_count'] = $totalcount;
                $response['current_page'] = $page;
                $response['next_page'] = $nextCountPage;
                $jsonResult->setData($response);
                return $jsonResult;
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
    /**
     * Check products is in wishlist or not.
     * @param  int $productID
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
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
    /**
     * Get product rating summary
     * @param  int $storeId
     * @param  int $productId
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    protected function getProductRatingSummary($storeId, $productId)
    {
        $reviewSummary = array('review_count' => null, 'rating_summary' => null);
        $model = $this->_reviewSummaryModel->setStoreId($storeId)->load($productId);
        $reviewSummary = array('review_count' => $model->getReviewsCount(), 'rating_summary' => $model->getRatingSummary());
        return $reviewSummary;
    }
}
