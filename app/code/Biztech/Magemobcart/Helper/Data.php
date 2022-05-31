<?php
/**
 *
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Data extends AbstractHelper
{
    const XML_ANDROID_KEY = 'magemobcart/magemobcart_general/authorization_key';
    const XML_IOS_KEY = 'magemobcart/magemobcart_general/upload_notification_file';
    const SECRET = "dfbDSkfdskf17hkfbgqwrsaFBfkdbf6bj";
    const KEY = 'secretkey';
    protected $storeManager;
    protected $scopeConfig;
    protected $encryptor;
    protected $jsonFactory;
    protected $customerSession;
    protected $customerModel;
    protected $wishlistModel;
    protected $productModel;
    protected $reviewSummaryModel;
    protected $catalogRule;
    protected $pricingHelper;
    protected $pricingInterface;
    protected $deviceData;
    protected $stockItemRepository;
    protected $dirCurrencyFactory;
    protected $mathRandom;
    protected $imageHelper;
    protected $messageManager;
    protected $categoryRepository;
    protected $categoryFactory;
    protected $productFactory;
    protected $productCustomOptions;
    protected $onepageCheckout;
    protected $formKeyData;
    protected $_objectFactory;
    protected $_designImageCollection;
    protected $_eventManager;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Wishlist\Model\Wishlist $wishlistModel,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\productFactory $productFactory,
        \Magento\Review\Model\Review\Summary $reviewSummaryModel,
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $pricingInterface,
        \Biztech\Magemobcart\Model\Devicedata $deviceData,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Directory\Model\CurrencyFactory $dirCurrencyFactory,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Product\Option $productCustomOptions,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        \Magento\Framework\Data\Form\FormKey $formKeyData,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Framework\Event\Manager $manager,
        \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designImageCollection
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->_customerSession = $customerSession;
        $this->_customerModel = $customerModel;
        $this->_wishlistModel = $wishlistModel;
        $this->_productModel = $productModel;
        $this->_reviewSummaryModel = $reviewSummaryModel;
        $this->_catalogRule = $catalogRule;
        $this->_pricingHelper = $pricingHelper;
        $this->_pricingInterface = $pricingInterface;
        $this->_deviceData = $deviceData;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_dirCurrencyFactory = $dirCurrencyFactory;
        $this->_mathRandom = $mathRandom;
        $this->_imageHelper = $imageHelper;
        $this->_messageManager = $messageManager;
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_productCustomOptions = $productCustomOptions;
        $this->_onepageCheckout = $onepageCheckout;
        $this->formKeyData = $formKeyData;
        $this->_objectFactory = $objectFactory;
        $this->_eventManager = $manager;
        $this->_designImageCollection = $designImageCollection;
        parent::__construct($context);
    }
    public function getCustomerByEmail($email, $websiteId) {
        $customer = $this->_customerModel;
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);
        return $customer;
    }
    public function createCustomerMultiWebsite($data, $websiteId, $storeId) {
        $customer = $this->_customerModel->setId(null);
        $customer->setFirstname($data['firstname'])
        ->setLastname($data['lastname'])
        ->setEmail($data['email'])
        ->setWebsiteId($websiteId)
        ->setStoreId($storeId)
        ->save();
        $length = 8;
        $chars = \Magento\Framework\Math\Random::CHARS_LOWERS
        . \Magento\Framework\Math\Random::CHARS_UPPERS
        . \Magento\Framework\Math\Random::CHARS_DIGITS;

        $newPassword = $this->_mathRandom->getRandomString($length, $chars);
        // $newPassword = $customer->generatePassword();
        $customer->setPassword($newPassword);
        try {
            $customer->save();
        } catch (\Exception $e) { }
        return $customer;
    }
    public function orderFormattedPrice($price, $currencyCode) {
        $orderCurrency = $this->_dirCurrencyFactory->create()->load($currencyCode);
        $orderCurrencySymbol = $orderCurrency->getCurrencySymbol();
        $formattedorderprice = $orderCurrency->formatTxt($price, array());
        return $formattedorderprice;
    }
    public function isEnable() {
        $websiteId = 1;
        $isenabled = $this->getScopeConfigValue('magemobcart/magemobcart_general/enabled');
        if ($isenabled) {
            if ($websiteId) {
                $websites = $this->getAllWebsites();
                $key = $this->getScopeConfigValue('magemobcart/activation/key');
                if ($key == null || $key == '') {
                    return false;
                } else {
                    $en = $this->getScopeConfigValue('magemobcart/activation/en');
                    if ($isenabled && $en) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $en = $this->getScopeConfigValue('magemobcart/activation/en');
                if ($isenabled && $en) {
                    return true;
                }
            }
        }
    }
    public function getScopeConfigValue($configPath)
    {
        return $this->_scopeConfig->getValue($configPath);
    }
    public function getAllStoreDomains()
    {
        $domains = array();
        foreach ($this->_storeManager->getWebsites() as $website) {
            $url = $website->getConfig('web/unsecure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
                $domains[] = $domain;
            }
            $url = $website->getConfig('web/secure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
                $domains[] = $domain;
            }
        }
        return array_unique($domains);
    }
    public function getAllWebsites()
    {
        if (!$this->getScopeConfigValue('magemobcart/activation/installed')) {
            return array();
        }
        $data = $this->getScopeConfigValue('magemobcart/activation/data');
        $web = $this->getScopeConfigValue('magemobcart/activation/websites');
        $websites = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = array_diff($websites, array(""));
        return $websites;
    }
    public function getDataInfo()
    {
        $data = $this->getScopeConfigValue('magemobcart/activation/data');
        return json_decode(base64_decode($this->_encryptor->decrypt($data)));
    }
    public function getFormatUrl($url)
    {
        $input = trim($url, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        // $domain = preg_replace('/^www\./', '', $urlParts['host'] . $urlParts['path']);
        if (isset($urlParts['path'])) {
            $domain = preg_replace('/^www\./', '', $urlParts['host'] . $urlParts['path']);
        } else {
            $domain = preg_replace('/^www\./', '', $urlParts['host']);
        }
        return $domain;
    }
    public function relogin($customerId)
    {
        $session = $this->_customerSession;
        $customerModel = $this->_customerModel->load($customerId);
        $session->setCustomerAsLoggedIn($customerModel);
    }
    public function getPriceByStoreWithCurrency($amount, $storeId)
    {
        return $this->_pricingHelper->currencyByStore($amount, $storeId, true, false);
    }
    public function getPriceByStoreWithoutCurrency($amount, $storeId)
    {
        $currentCurrency = $this->_storeManager->getStore($storeId)->getCurrentCurrency();
        return $this->_pricingInterface->convert($amount, $storeId, $currentCurrency);
    }
    public function getWishlistData($customerId, $storeId)
    {
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
        $wishlist = $this->_wishlistModel->loadByCustomerId($customerId, true);
        $wishlistCollection = array();
        $wishListItemCollection = $wishlist->getItemCollection();

        foreach ($wishListItemCollection->getData() as $_item) {
            $byiUrl = '';
            $isByiExists = '';
            $isAddToCart = '';
            $productData = $this->_productFactory->create()->load($_item['product_id']);
            $summaryData = $this->_reviewSummaryModel->setStoreId($storeId)->load($_item['product_id']);
            if ($this->_catalogRule->calcProductPriceRule($productData, $productData->getPrice())) {
                $specialPrice = $this->_catalogRule->calcProductPriceRule($productData, $productData->getPrice());
            } else {
                $specialPrice = $productData->getSpecialPrice();
            }
            if ($productData->getTypeId() == 'grouped') {
                $aProductIds = $productData->getTypeInstance()->getChildrenIds($product_data->getId());
                $prices = array();
                foreach ($aProductIds as $ids) {
                    foreach ($ids as $id) {
                        $aProduct = $this->_productFactory->create()->load($id);
                        $prices[] = $aProduct->getPriceModel()->getPrice($aProduct);
                    }
                }
                krsort($prices);
                $price_array = array_keys($prices);
                $price = $this->getPriceByStoreWithCurrency(min($prices), $storeId);
            } elseif ($productData->getTypeId() == 'configurable') {
                $price = $this->getPriceByStoreWithCurrency($productData->getFinalPrice(), $storeId);
            } else {
                $price = $this->getPriceByStoreWithCurrency($productData->getPrice(), $storeId);
            }
            // $productStockData = $this->_stockItemRepository->get($_item['product_id']);
            $isInStock = $productData->getExtensionAttributes()->getStockItem()->getIsInStock();
            $product = array(
                'sku' => $productData->getSku(),
                'name' => $productData->getName(),
                'status' => $productData->getStatus(),
                'type' => $productData->getTypeId(),
                // 'in_stock' => (string) $productStockData->getIsInStock(),
                'in_stock' => (string) $isInStock,
                'review_count' => $summaryData->getReviewsCount(),
                'rating_summary' => $summaryData->getRatingSummary(),
                'short_desc' => strip_tags($productData->getShortDescription()),
                'price' => $price,
                'image' => $this->_imageHelper->init($productData, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                'special_price' => $this->getPriceByStoreWithCurrency($specialPrice, $storeId),
                'byi_url_id' => $isByiExists,
                'byi_url' => $byiUrl,
                'has_custom_option' => $this->hasCustomOption($productData),
                'save_discount' => $this->getDiscount($productData->getProductId()),
                'is_addtocart' => $isAddToCart
            );

            $product['id'] = $_item['product_id'];
            $customObject = $this->_objectFactory->create();
            $customObject->setProductData($product);
            $this->_eventManager->dispatch('get_wishlist_data_before', ['productData' => $customObject]);
            $product = $customObject->getProductData();
            // unset($product['id']);
            
            $wishlistCollection[] = array(
                'wishlist_item_id' => $_item['wishlist_item_id'],
                'wishlist_id' => $_item['wishlist_id'],
                'product_id' => $_item['product_id'],
                'store_id' => $_item['store_id'],
                'added_at' => $_item['added_at'],
                'description' => $_item['description'],
                'qty' => $_item['qty'],
                'product_detail' => $product
            );
        }
        return $wishlistCollection;
    }
    public function getCartData($items, $totals, $action, $param = null, $storeId)
    {
        $jsonResult = $this->_jsonFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
        $designId = "";

        if (!empty($items)) {
            $productDetailResult = array();
            $temp_items = array();
            $wishlist_items = array();
            if ($this->_customerSession->isLoggedIn()) {
                $customerData = $this->_customerSession->getCustomer();
                $wishList = $this->_wishlistModel->loadByCustomerId($customerData->getEntityId());
                $wishListItemCollection = $wishList->getItemCollection();
                foreach ($wishListItemCollection as $itemValue) {
                    $wishlist_items[] = $itemValue->getProductId();
                }
            }
            $option = array();
            $discountTotal = '';
            foreach ($items as $item) {
                $types[] = $item->getProductType();
                $message = '';
                $options = array();
                if ($item->getProductType() == 'configurable') {
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $customOptions = $options['attributes_info'];
                    $options = $options['attributes_info'];
                    // echo "<pre>";
                    // print_r($options);
                    // die();
                    if (!empty($customOptions)) {
                        foreach ($customOptions as $option1) {
                            $option = array(
                                'label' => $option1['label'],
                                'option_id' => $option1['option_id'],
                                'value' => $option1['value']
                            );
                        }
                    }
                    // echo "<pre>";
                    // print_r($customOptions);
                    // die();
                } else {
                    $options = array();
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    

                    if (array_key_exists('options', $options)) {
                        $customOptions = $options['options'];
                        foreach ($customOptions as $optionKey => $valuesVal) {
                            $options[] = array(
                                'label' => $valuesVal['label'],
                                'option_id' => $valuesVal['option_id'],
                                'value' => $valuesVal['value']
                            );
                        }
                    }
                }
                $additional_options = $item->getOptionByCode('additional_options');
                $designId = "";
                if (!empty($additional_options)) {
                    $options = array();
                    foreach ($additional_options->getData() as $optionKey => $valuesVal) {
                        if ($optionKey == 'value') {
                            $valuesVal = json_decode($valuesVal, true);
                            $designId = $valuesVal[0]['design_id'];
                        }
                    }
                }
                $product_data = $this->_productFactory->create()->load($item->getProductId());
                $links_name = array();
                if ($item->getProductType() == 'downloadable') {
                    $linksOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $title = $item->getProduct()->getLinksTitle();
                    if (!isset($title)) {
                        $title = "";
                    }
                    if (isset($linksOptions['links']) && is_array($linksOptions['links'])) {
                        $_myprodlinks = $objectManager->get('Magento\Downloadable\Model\Link')->load(10);
                        $_myLinksCollection = $_myprodlinks->getCollection()->addProductToFilter($item->getProduct()->getId());
                        foreach ($_myLinksCollection->getData() as $key => $valuelinkCollection) {
                            foreach ($linksOptions['links'] as $key => $valueOptions) {
                                if ($valuelinkCollection['link_id'] == $valueOptions) {
                                    $links_name['title'] = $title;
                                    // $links_name['link_details'][] = $valuelinkCollection['title'];
                                }
                            }
                        }
                    }
                }
                if ($item->getHasError() == 1) {
                    $message = $item->getMessage();
                }
                $design_id = '';
                $params = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                // if (isset($params['info_buyRequest']['design'])) {
                //     $design_id = $params['info_buyRequest']['design'];
                // }
                // if (isset($design_id) && $design_id) :
                //     $designImages = "N/A";
                //     $p_img = "N/A";
                // else :
                //         $p_img = "N/A";
                // endif;

                // $productDetailResult['is_contain_virtual_product'] = $f;
                if (in_array($item->getProduct()->getId(), $wishlist_items)) {
                    $is_wishlist = true;
                } else {
                    $is_wishlist = false;
                }
                $productModelData = $this->_productFactory->create()->load($item->getProduct()->getId());
                /********************Designer Image****************************/
                $pimg = $this->_imageHelper->init($productModelData, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl();

                $itemData = array(
                    'product_id' => $item->getProduct()->getId(),
                    'name' => $item->getName(),
                    'type' => $item->getProductType(),
                    'price' => $this->getPriceByStoreWithCurrency($item->getPrice(), $storeId),
                    'row_total' => $this->getPriceByStoreWithCurrency($item->getRowTotal(), $storeId),
                    // 'pimg' => $this->_imageHelper->init($productModelData, 'product_page_image_medium')->resize(300, 330)->constrainOnly(true)->keepAspectRatio(true)->getUrl(),
                    'pimg' => $pimg,
                    'qty' => $item->getQty(),
                    'disAmount' => $this->getPriceByStoreWithCurrency($item->getDiscountAmount(), $storeId),
                    'options' => $options,
                    'links' => $links_name,
                    'is_wishlist' => $is_wishlist
                );
                if($designId != ""){

                 $customObject = $this->_objectFactory->create();
                 $customObject->setProductImage($pimg);
                 $this->_eventManager->dispatch('customer_shoppinglist_response_before', ['productImage' => $customObject, 'designId'=>$designId]);
                 $pimg = $customObject->getProductImage();
                 $itemData['pimg'] = $pimg;
                 $itemData['byi_url'] = '';
                 $itemData['byi_url_id'] = 'true';
                 $itemData['designId'] = base64_encode($designId);
                 $itemData['itemId'] = base64_encode($item->getId());
             }

             /********************Designer Image****************************/
             $temp_items[$item->getId()] = $itemData;
             $discountTotal = (float) $discountTotal + $item->getDiscountAmount();
             if (!empty($message)) {
                $temp_items[$item->getId()]['message'] = $message;
            }
            unset($product_data);
        }
        $t = array_unique($types);
        $f = 1;

        $ty = array('simple', 'virtual');
        foreach ($t as $value) {
                // echo $value;
            if ($value != 'downloadable' && $value != 'virtual') {
                $f = 0;
                break;
            } elseif ($value == 'downloadable') {
                $f = 1;
            } elseif ($value == 'virtual') {
                $f = 1;
            } else {
                $f = 0;
                break;
            }
        }
        $productDetailResult['is_contain_virtual_product'] = $f;
        $productDetailResult['quote_id'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuoteId();
        $productDetailResult['items'] = $temp_items;
        $productDetailResult['totals'] = array(
            'subtotal' => $this->getPriceByStoreWithCurrency($totals['subtotal']->getData('value'), $storeId),
            'grand_total' => $this->getPriceByStoreWithCurrency($totals['grand_total']->getData('value'), $storeId)
        );
        if ($totals['shipping']) {
            $productDetailResult['totals']['shipping'] = $this->getPriceByStoreWithCurrency($totals['shipping']->getData('value'), $storeId);
        }
        if ($totals['tax']) {
            $productDetailResult['totals']['tax'] = $this->getPriceByStoreWithCurrency($totals['tax']->getData('value'), $storeId);
        }
        if (isset($discountTotal)) {
            $productDetailResult['totals']['discount'] = $this->getPriceByStoreWithCurrency($discountTotal, $storeId);
        }
        if ($action == 'add') {
            $productDetailResult['status'] = 'success';
            $productDetailResult['message'] = 'Product Added Successfully';
            $productDetailResult['cart_count'] = count($productDetailResult['items']);
        } elseif ($action == 'cart_remove') {
            $productDetailResult['status'] = 'success';
            $productDetailResult['message'] = 'Product Removed Successfully.';
            $productDetailResult['cart_count'] = count($productDetailResult['items']);
        } elseif ($action == 'coupon') {
            $productDetailResult['status'] = 'success';
            $productDetailResult['message'] = 'Coupon code was applied.';
            $productDetailResult['coupon_code'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getCouponCode();
        } elseif ($action == 'coupon_remove') {
            $productDetailResult['status'] = 'success';
            $productDetailResult['message'] = 'Coupon code was canceled.';
            $productDetailResult['coupon_code'] = '';
        } elseif ($action == 'displaycart') {
            if ($objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getCouponCode()) {
                $productDetailResult['coupon_code'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getCouponCode();
            } else {
                $productDetailResult['coupon_code'] = '';
            }
        }
        if ($objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getCouponCode()) {
            $productDetailResult['coupon_code'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getCouponCode();
        }
        if ($param == 'save_payment') {
            if ($this->_customerSession->isLoggedIn()) {
                $customerData = $this->_customerSession->getCustomer();
                $customerid = $customerData->getId();
            }
            $productDetailResult['payment_url'] = $this->_storeManager->getStore()->getBaseUrl() . 'magemobcart/checkout/processing/?quoteid=' . $objectManager->get('Magento\Checkout\Model\Session')->getQuoteId() . '&customer_id=' . $customerid . '&storeid=' . $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getStoreId();
            $productDetailResult['payment_url_android'] = $this->_storeManager->getStore()->getBaseUrl() . 'magemobcart/checkout/customerlogin/?customer_id=' . $customerid;
        }
        if ($param == 'save_shipping') {
            if ($this->_customerSession->isLoggedIn()) {
                $customerData = $this->_customerSession->getCustomer();
                $customerid = $customerData->getId();
            }
            $productDetailResult['payment_url'] = $this->_storeManager->getStore()->getBaseUrl() . 'magemobcart/checkout/getpaymenthtml/?quoteid=' . $objectManager->get('Magento\Checkout\Model\Session')->getQuoteId() . '&customer_id=' . $customerid . '&storeid=' . $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getStoreId();
        }
        $productDetailResult['quote_id'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getId();
        $productDetailResult['base_grand_total'] = (string) $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getBaseGrandTotal();
        $productDetailResult['base_currency_code'] = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getBaseCurrencyCode();

        $customObject = $this->_objectFactory->create();
        $customObject->setCartData($productDetailResult);
        $this->_eventManager->dispatch('get_cart_response_before', ['cartResponse' => $customObject]);
        $productDetailResult = $customObject->getCartData();

            // $returnExtensionArray = array($productDetailResult);
        $jsonResult->setData($productDetailResult);
        return $jsonResult;
    } else {
        $productDetailResult = array(
            'status' => 'success',
            'message' => 'Shopping cart is empty'
        );
        $jsonResult->setData($productDetailResult);
        return $jsonResult;
    }
}
public function getDiscount($productId)
{
    $productCollection = $this->_productFactory->create()->load($productId);
    $price = $productCollection->getPrice();
    $specialPrice = $productCollection->getSpecialPrice();
    $productSpecialPrice = $productCollection->getSpecialPrice();
    if ($specialPrice && $price) {
        if ($specialPrice < $price) {
            $savePercent = $specialPrice * 100 / $price;
        } else {
            $savePercent = 0;
            return round($savePercent);
        }
    } else {
        $savePercent = 0;
        return round($savePercent);
    }
    return round(100 - $savePercent);
}
public function create($post_data)
{
    $collectionDevice = $this->_deviceData->getCollection();
    $deviceModel = $this->_deviceData;

    foreach ($collectionDevice as $device) {
        $token = $device->getDeviceToken();
        $CustomerEmail = $device->getCustomerEmail();

        $fields = array();

        if (isset($token) && $token != null && ($post_data['devicetoken'] == $token)) {
            if ($post_data['is_logout'] != 1) {
                if (isset($CustomerEmail) && $CustomerEmail != null) {
                    if ($post_data['username'] != $CustomerEmail) {
                        if ($device->getId()) { }
                            $fields['customer_email'] = $post_data['username'];
                        $fields['password'] = $post_data['password'];
                        $fields['customer_id'] = $post_data['customer_id'];
                        $fields['is_logout'] = $post_data['is_logout'];

                            // $prefix = Mage::getConfig()->getTablePrefix();
                            // $connection->update($prefix . 'magemob_devicedata', $fields, $where);
                            // $connection->commit();
                    } else {
                        $fields['password'] = $post_data['password'];
                        $fields['customer_id'] = $post_data['customer_id'];
                        $fields['is_logout'] = $post_data['is_logout'];

                            // $prefix = Mage::getConfig()->getTablePrefix();
                            // $connection->update($prefix . 'magemob_devicedata', $fields, $where);
                            // $connection->commit();
                    }
                } else {
                    if ($device->getDeviceToken() == $post_data['devicetoken']) {
                        $updateDeviceData = $this->_deviceData->load($device->getId());
                        $updateDeviceData->setCustomerEmail($post_data['username']);
                        $updateDeviceData->setPassword($post_data['password']);
                        $updateDeviceData->setIsLogout($post_data['is_logout']);
                        $updateDeviceData->setCustomerId($post_data['customer_id']);
                        $updateDeviceData->save();
                    }
                }
            }
        }
    }
}
public function sendNotification($data)
{
    $result = $this->send($data);
    if ($result) {
        $this->_messageManager->addSuccess(__("Message successfully delivered."));
    } else {
        $this->_messageManager->addError(__("Message not delivered."));
    }
    return $result;
}
public function send($data)
{
    $website = $data['website_id'];
    $collectionDevice = $this->_deviceData->getCollection();
    if (isset($data['notification_id'])) {
        $collectionDevice = $collectionDevice->addFieldToFilter('cron_status', 'pending');
    }
    if ($data['os'] == 'all') {
        $resultAndroid = $this->sendAndroid($collectionDevice, $data);
        $resultIOS = $this->sendIOS($collectionDevice, $data);
        if ($resultIOS || $resultAndroid) {
            return true;
        } else {
            return false;
        }
    } elseif ($data['os'] == 'android') {
        return $this->sendAndroid($collectionDevice, $data);
    } elseif ($data['os'] == 'ios') {
        return $this->sendIOS($collectionDevice, $data);
    }
}
public function sendIOS($collectionDevice, $data)
{
    $collectionDeviceios = $this->_deviceData->getCollection()
    ->addFieldToFilter('device_type', 'ios')
    ->addFieldToFilter('device_token', 'd2I0RBeeMkQ:APA91bGhspvrNSzcfsXIwPGUaGGGKFglGAa--6ukcfPiLcQBzWjyWmTGPY5gQO_nujHLjnmEbuTubeyRrRbohaTgRQ3FgsCVdiNZ_S53Y4x4O2noJykXzyTfp7KBI2OrXE0tmdJK42ZC');

        // ->addFieldToFilter('device_id', array('neq'=> null));
    if (!array_key_exists("flag", $data)) {
        $collectionDeviceios->addFieldToFilter('cron_status', 'pending');
    }
    $collectionDeviceios->getSelect()->group('device_id');
    $deviceModel = $this->_deviceData;
    $iosKey = $this->_scopeConfig->getValue(self::XML_IOS_KEY);
    $message = array();
    if (array_key_exists('message', $data)) {
        if (!empty($data['message']) && $data['message'] != null) {
            $message['notification']['body'] = $data['message'];
        }
    }
    if (array_key_exists('url', $data)) {
        if (!empty($data['url']) && $data['url'] != null) {
            $message['notification']['url'] = $data['url'];
        }
    }
    if (array_key_exists('title', $data)) {
        if (!empty($data['title']) && $data['title'] != null) {
            $message['notification']['title'] = $data['title'];
        }
    }
    if (array_key_exists('flag', $data)) {
        if (isset($data['flag']) && $data['flag'] == 1) { }
    }
if (array_key_exists('category_id', $data)) {
    if (!empty($data['category_id']) && $data['category_id'] > 0) {
        $_category = $this->_categoryFactory->create()->load($data['category_id']);
        $message['notification']['category_name'] = $_category->getName();
        $message['notification']['category_id'] = $data['category_id'];
    }
}
if (array_key_exists('product_id', $data)) {
    if (!empty($data['product_id']) && $data['product_id'] != null) {
        $message['notification']['product_id'] = $data['product_id'];
    }
}
if (array_key_exists('entity_id', $data)) {
    if (!empty($data['entity_id']) && $data['entity_id'] != null) {
        $message['notification']['entity_id'] = $data['entity_id'];
    }
}
if (array_key_exists('imagefilename', $data)) {
    if (!empty($data['imagefilename']) && $data['imagefilename'] != null) {
        $message['data']['imagefilename'] = $data['imagefilename'];
        $message['notification']['content_available'] = "true";
        $message['notification']['mutable_content'] = "true";
    }
}
$url = "https://fcm.googleapis.com/fcm/send";
$headers = array(
    'Authorization: key=' . $iosKey,
    'Content-Type: application/json'
);
$registrationIDs = array();
foreach ($collectionDeviceios as $item) {
    $customer_email = $item['customer_email'];
    if (isset($data['flag']) && $data['flag'] == 1) {
        if (isset($customer_email) && $customer_email == $data['customer_email']) {
            $registrationIDs[] = $item['device_token'];
        }
        continue;
    } else {
        if (array_key_exists('notification_id', $data)) {
            if ($item->getNotificationId() !== null) {
                $notiId = array();
                $notiId = explode(',', $item->getNotificationId());
                if (in_array($data['notification_id'], $notiId)) {
                    $registrationIDs[] = $item->getDeviceToken();
                    if (($key = array_search($data['notification_id'], $notiId)) !== false) {
                        unset($notiId[$key]);
                    }
                    if (!empty($notiId)) {
                        $notiId = implode(',', $notiId);
                        $deviceModel->setCronStatus('pending')
                        ->setNotificationID($notiId)
                        ->setId($item->getId())
                        ->save();
                    } else {
                        $notiId = null;
                        $deviceModel->setCronStatus('completed')
                        ->setNotificationID($notiId)
                        ->setId($item->getId())
                        ->save();
                    }
                }
            }
        }
    }
}
if (count($registrationIDs) > 1000) {
    $idArray = array();
    $idArray = array_chunk($registrationIDs, 1000);
    foreach ($idArray as $tokenId) {
        $fields = array(
            'registration_ids' => $tokenId,
            'data' => array("message" => $message),
        );
        $result = '';
        $result = $this->iosCurlRequest($url, $headers, $fields);
    }
} else {
    $result = '';
    $result = $this->iosCurlRequest($url, $headers, $registrationIDs, $message);
}
$re = json_decode($result);
/*$this->_messageManager->addSuccess(__("Message successfully delivered (IOS)"));*/
return true;
}
public function iosCurlRequest($url, $headers, $registrationIDs, $data)
{
    $url = "https://fcm.googleapis.com/fcm/send";

    foreach ($registrationIDs as $key => $id) {
        $data['to'] = $id;
        $data['notification']['sound'] = "default";
        $json = json_encode($data);
        $iosKey = $this->_scopeConfig->getValue(self::XML_IOS_KEY);
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $iosKey
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
public function sendAndroid($collectionDevice, $data)
{
    $collectionDeviceandroid = $this->_deviceData->getCollection()
    ->addFieldToFilter('device_type', 'android');
        // ->addFieldToFilter('device_token', 'cO0T5mblx5g:APA91bFYZXdX1g-3ZlkOf7zi9xYXKg_0vxA5gLxiSsZPsdqJyVgVmLjm3jeIWt1hP8LAVr803SM-uAhqHPqLsB4si8x-os0d0UG4IirQccJNEUIjDTgMX_HWGL0Ns0oPOxXCqDyE4M-r');
    if (!array_key_exists("flag", $data)) {
        $collectionDeviceandroid->addFieldToFilter('cron_status', 'pending');
    }
    $deviceModel = $this->_deviceData;
    $apiKey = $this->_scopeConfig->getValue(self::XML_ANDROID_KEY);
    $message = array();
    if (array_key_exists('message', $data)) {
        if (!empty($data['message']) && $data['message'] != null) {
            $message['message'] = $data['message'];
        }
    }
    if (array_key_exists('url', $data)) {
        if (!empty($data['url']) && $data['url'] != null) {
            $message['url'] = $data['url'];
        }
    }
    if (array_key_exists('title', $data)) {
        if (!empty($data['title']) && $data['title'] != null) {
            $message['title'] = $data['title'];
        }
    }
    if (array_key_exists('flag', $data)) {
        if (isset($data['flag']) && $data['flag'] == 1) {
            $message['type'] = 'order';
        }
    }
    if (array_key_exists('type', $data)) {
        if (!empty($data['type']) && $data['type'] != null) {
            $message['type'] = $data['type'];
        }
    }
    if (array_key_exists('category_id', $data)) {
        if (!empty($data['category_id']) && $data['category_id'] != null) {
            $_category = $this->_categoryFactory->create()->load($data['category_id']);
            $message['category_name'] = $_category->getName();
            $message['category_id'] = $data['category_id'];
        }
    }
    if (array_key_exists('product_id', $data)) {
        if (!empty($data['product_id']) && $data['product_id'] != null) {
            $message['product_id'] = $data['product_id'];
        }
    }
    if (array_key_exists('entity_id', $data)) {
        if (!empty($data['entity_id']) && $data['entity_id'] != null) {
            $message['entity_id'] = $data['entity_id'];
        }
    }
    if (array_key_exists('imagefilename', $data)) {
        if (!empty($data['imagefilename']) && $data['imagefilename'] != null) {
            $message['imagefilename'] = $data['imagefilename'];
        }
    }
    $url = "https://fcm.googleapis.com/fcm/send";
    $headers = array(
        'Authorization: key=' . $apiKey,
        'Content-Type: application/json'
    );

    $registrationIDs = array();
    foreach ($collectionDeviceandroid as $item) {
        $customer_email = $item['customer_email'];
        if (isset($data['flag']) && $data['flag'] == 1) {
            if (isset($customer_email) && $customer_email == $data['customer_email']) {
                $registrationIDs[] = $item['device_token'];
            }
        } else {
            if (array_key_exists('notification_id', $data)) {
                if ($item->getNotificationId() !== null) {
                    $notiId = array();
                    $notiId = explode(',', $item->getNotificationId());
                    if (in_array($data['notification_id'], $notiId)) {
                        $registrationIDs[] = $item->getDeviceToken();
                        if (($key = array_search($data['notification_id'], $notiId)) !== false) {
                            unset($notiId[$key]);
                        }
                        if (!empty($notiId)) {
                            $notiId = implode(',', $notiId);
                            $deviceModel->setCronStatus('pending')
                            ->setNotificationID($notiId)
                            ->setId($item->getId())
                            ->save();
                        } else {
                            $notiId = null;
                            $deviceModel->setCronStatus('completed')
                            ->setNotificationID($notiId)
                            ->setId($item->getId())
                            ->save();
                        }
                    }
                }
            }
        }
    }
    if (count($registrationIDs) > 1000) {
        $idArray = array();
        $idArray = array_chunk($registrationIDs, 1000);
        foreach ($idArray as $tokenId) {
            $fields = array(
                'registration_ids' => $tokenId,
                'data' => array("message" => $message),
            );
            $result = '';
            $result = $this->curlRequest($url, $headers, $fields);
        }
    } else {
        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => array("message" => $message),
        );
        $result = '';
        $result = $this->curlRequest($url, $headers, $fields);
    }
    $re = json_decode($result);
        // if ($re->success == 0) {
        //     $this->_messageManager->addError(__("Message not delivered (Android)"));
        //     return false;
        // }
        // $this->_messageManager->addSuccess(__("Message successfully delivered (Android)"));
    return true;
}
public function curlRequest($url, $headers, $fields)
{
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
    } catch (\Exception $e) { }
    return $result;
}
public function hasCustomOption($product)
{
    $customOptions = $this->_productCustomOptions->getProductOptionCollection($product);

    if (count($customOptions) > 0) {
        return true;
    }
    return false;
}
public function getFormKey()
{
    $formKey = $this->formKeyData->getFormKey();
    return $formKey;
}
public function getTierPrice($productId, $customerId)
{
    $groupId = $this->getCustomerGroupDetail($customerId);
    $groupId = 0;
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
    $productAllTierPrices = $_product->getData('tier_price');
    $allTierPrices = array();

    foreach ($productAllTierPrices as $tierPrices) {
        if ($tierPrices['cust_group'] == $groupId) {
            $allTierPrices[] = "Buy " . $tierPrices['price_qty'] . " for " . $priceHelper->currency($tierPrices['price'], true, false) . " each and save " . $this->getGroupDiscount($_product->getFinalPrice(), $tierPrices['price']) . "%";
        }
    }
    return $allTierPrices;
}
public function getCustomerGroupDetail($customerId)
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $customerModel = $objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
    $customerGroupId = $customerModel->getGroupId();
    return $customerGroupId;
}
public function getGroupDiscount($price, $specialPrice)
{
    if ($specialPrice && $price) {
        if ($specialPrice < $price) {
            $savePercent = $specialPrice * 100 / $price;
        } else {
            $savePercent = 0;
            return round($savePercent);
        }
    } else {
        $savePercent = 0;
        return round($savePercent);
    }
    return round(100 - $savePercent);
}
    /**
     * [getHeaderInfo use for the create Header if already headler function is not there]
     * @return [type] [reutrn headers value]
     */

    public function getHeaderInfo()
    {
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = array();
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
                    } else {
                        $headers[$name] = $value;
                    }
                }
                return $headers;
            }
        }
    }

    /**
     * [getHeaders use for the get the all requested call headers]
     * @return [type] [retuen headers value]
     */

    public function getHeaders()
    {
        $allHeaders = array();
        $headersSecretKey = "";
        $this->getHeaderInfo();
        $allHeaders = getallheaders();
        if (!array_key_exists(self::KEY, $allHeaders)) {
            if (array_key_exists('Secretkey', $allHeaders)) {
                $headersSecretKey = $allHeaders['Secretkey'];
            }
            $allHeaders[self::KEY] = $headersSecretKey;
            unset($allHeaders['Secretkey']);
        }
        $orignalValue = base64_encode(self::SECRET);
        $key = strtolower(self::KEY);
        foreach ($allHeaders as $name => $value) {
            if (array_key_exists(self::KEY, $allHeaders)) {
                if ($name == self::KEY) {
                    if (isset($value)) {
                        if (strcasecmp($value, $orignalValue) == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
    }
    public function getHeaderMessage()
    {
        return 'Please make sure your app is proper authenticated';
    }
    public function addTokenGeneralLogin($postData)
    {
        $collectionDevice = $this->_deviceData->getCollection();
        $collectionDevice->addFieldToFilter('device_token', $postData['devicetoken']);
        if (empty($collectionDevice->getData())) {
            $updateDeviceData = $this->_deviceData;
            $updateDeviceData->setCustomerEmail($postData['username']);
            $updateDeviceData->setCustomerId($postData['customer_id']);
            $updateDeviceData->setPassword($postData['password']);
            $updateDeviceData->setWebsiteId(1);
            $updateDeviceData->setIsLogout(0);
            $updateDeviceData->setCustomerId();
            $updateDeviceData->setDeviceToken($postData['devicetoken']);
            $updateDeviceData->setDeviceId($postData['devicetoken']);
            $updateDeviceData->save();
        } else {
            foreach ($collectionDevice->getData() as $key => $value) {
                if ($postData['username'] != $value['customer_email']) {
                    $updateModel = $this->_deviceData->load($value['id']);
                    $updateModel->setCustomerEmail($postData['username']);
                    $updateModel->setCustomerId($postData['customer_id']);
                    $updateModel->setPassword($postData['password']);
                    $updateModel->setIsLogout($postData['is_logout']);
                    $updateModel->save();
                }
            }
        }
    }
}
