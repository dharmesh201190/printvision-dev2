<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Getorderdetail extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $storeManager;
    protected $productModel;
    protected $productFactory;
    protected $orderModel;
    protected $shippingHelper;
    protected $downloadableLinkPurchased;
    protected $orderItemModel;
    protected $timezone;
    protected $imageHelper;
    protected $directoryModel;
    protected $formKey;

    /**
     * @param Context                                              $context
     * @param JsonFactory                                          $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data                     $cartHelper
     * @param \Magento\Framework\App\Request\Http                  $request
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Catalog\Model\Product                       $productModel
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory
     * @param \Magento\Sales\Model\Order                           $orderModel
     * @param \Magento\Shipping\Helper\Data                        $shippingHelper
     * @param \Magento\Downloadable\Model\Link\Purchased           $downloadableLinkPurchased
     * @param \Magento\Sales\Model\Order\Item                      $orderItemModel
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Catalog\Helper\Image                        $imageHelper
     * @param \Magento\Directory\Model\Country                     $directoryModel
     * @param \Magento\Framework\Data\Form\FormKey                 $formKey
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Shipping\Helper\Data $shippingHelper,
        \Magento\Downloadable\Model\Link\Purchased $downloadableLinkPurchased,
        \Magento\Sales\Model\Order\Item $orderItemModel,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Directory\Model\Country $directoryModel,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_productModel = $productModel;
        $this->_productFactory = $productFactory;
        $this->_orderModel = $orderModel;
        $this->_shippingHelper = $shippingHelper;
        $this->_downloadableLinkPurchased = $downloadableLinkPurchased;
        $this->_orderItemModel = $orderItemModel;
        $this->_timezone = $timezone;
        $this->_imageHelper = $imageHelper;
        $this->_directoryModel = $directoryModel;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
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
                $errorResult = array('status' => false, 'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customerId = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customerId[0]);
                }
            }
            if (array_key_exists('storeid', $postData) && array_key_exists('order_entity_id', $postData)) {
                $storeId = $postData['storeid'];
                $orderId = $postData['order_entity_id'];
                $designId = null;
                if ($storeId != "" && $orderId != "") {
                    $order = $this->_orderModel->load($orderId);
                    $trackingUrl = '';

                    if ($order->getTracksCollection()->count()) {
                        $trackingUrl = $this->_shippingHelper->getTrackingPopupUrlBySalesModel($order);
                    }

                    $orderDetail = array(
                        'entity_id' => $order->getEntityId(),
                        'increment_id' => $order->getIncrementId(),
                        'status' => $order->getStatus(),
                        'order_date' => $this->_timezone->date(new \DateTime($order->getCreatedAt()))->format('Y-m-d H:i:s'),
                        'total_qty' => round($order->getTotalQtyOrdered(), 2),
                        'grand_total' => $this->_cartHelper->orderFormattedPrice($order->getGrandTotal(), $order->getOrderCurrencyCode()),
                        'sub_total' => $this->_cartHelper->orderFormattedPrice($order->getSubtotal(), $order->getOrderCurrencyCode()),
                        'discount' => $this->_cartHelper->orderFormattedPrice($order->getDiscountAmount(), $order->getOrderCurrencyCode()),
                        'base_tax_amount' => $this->_cartHelper->orderFormattedPrice($order->getTaxAmount(), $order->getOrderCurrencyCode()),
                        'tracking_url' => $trackingUrl
                    );
                    $customerId = $order->getCustomerId();
                    $customerName = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
                    if ($customerId == null) {
                        $customerName = $order->getCustomerName();
                    }
                    $customer_detail = array(
                        'customer_id' => $customerId,
                        'customer_name' => $customerName,
                        'customer_email' => $order->getCustomerEmail()
                    );
                    $billingAddress = $order->getBillingAddress();

                    $billingAddressData = array(
                        'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
                        'street' => $billingAddress->getData('street'),
                        'city' => $billingAddress->getCity(),
                        'region' => $billingAddress->getRegion(),
                        'postcode' => $billingAddress->getPostcode(),
                        'country' => $this->_directoryModel->loadByCode($billingAddress->getCountryId())->getName(),
                        'telephone' => $billingAddress->getTelephone()
                    );
                    $shippingAddress = $order->getShippingAddress();
                    $shippingAddress_data = array();
                    if ($shippingAddress) {
                        $shippingAddress_data = array(
                            'name' => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                            'street' => $shippingAddress->getData('street'),
                            'city' => $shippingAddress->getCity(),
                            'region' => $shippingAddress->getRegion(),
                            'postcode' => $shippingAddress->getPostcode(),
                            'country' => $this->_directoryModel->loadByCode($shippingAddress->getCountryId())->getName(),
                            'telephone' => $shippingAddress->getTelephone()
                        );
                    }
                    $paymentInfo = array(
                        'payment_method' => $order->getPayment()->getMethodInstance()->getTitle()
                    );
                    $shippingInfo = array(
                        'shipping_method' => $order->getShippingDescription(),
                        'shipping_charge' => $this->_cartHelper->orderFormattedPrice($order->getShippingAmount(), $order->getOrderCurrencyCode()),
                    );
                    $isCanceled = false;
                    if ($order->getStatus() == "pending") {
                        $isCanceled = true;
                    }
                    $productsDetail = $this->_orderedProductDetails($orderId, $storeId);

                    $customObject = $this->_objectFactory->create();
                    $customObject->setProductData($productsDetail);
                    $this->_eventManager->dispatch('orderdetail_response_before', ['productDetailData' => $customObject, 'orderData'=>$order]);
                    $productsDetail = $customObject->getProductData();
                    
                    $fullOrderDetail = array(
                        'basic_order_detail' => $orderDetail,
                        'customer_detail' => $customer_detail,
                        'billing_address' => $billingAddressData,
                        'shipping_address' => $shippingAddress_data,
                        'payment_info' => $paymentInfo,
                        'shipping_info' => $shippingInfo,
                        'product_detail' => $productsDetail,
                        'tracking_detail' => $this->getTrackingInformation($orderId),
                        'is_invoice' => (bool) $order->hasInvoices(),
                        'is_cancel' => $isCanceled,
                        'is_email' => !$order->isCanceled(),
                        'is_reorder' => $order->canReorder()
                    );
                    $orderDetailResultArr = array('orderlistdata' => $fullOrderDetail);
                    $jsonResult->setData($orderDetailResultArr);
                    return $jsonResult;
                } else {
                    $returnExtensionArray = array('status' => 'false', 'message' => 'Store or order should not blank');
                    $jsonResult->setData($returnExtensionArray);
                    return $jsonResult;
                }
            } else {
                $returnExtensionArray = array('status' => 'false', 'message' => 'Store or order is missing');
                $jsonResult->setData($returnExtensionArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function _orderedProductDetails($orderId, $storeId)
    {
        $medialUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $order = $this->_orderModel->load($orderId);
        $info = array();
        $finalLink = "";
        $links_value = array();
        $this->_purchased = $this->_downloadableLinkPurchased->load($orderId, 'order_id');

        foreach ($order->getItemsCollection() as $item) {
            $options = $item->getProductOptions();

            if ($item->getProductType() == "downloadable") {
                $purchasedItem = $this->_downloadableLinkPurchased->getCollection()
                    ->addFieldToFilter('order_item_id', $item->getId());
                $this->_purchased->setPurchasedItems($purchasedItem);

                foreach ($this->_purchased->getPurchasedItems() as $_link) {
                    if ($_link->getLink_type() == 'file') {
                        $finalLink = $medialUrl . "downloadable/download/link/id/" . $_link->getLinkHash();
                    } elseif ($_link->getLink_type() == 'url') {
                        $finalLink = $_link->getLink_url();
                    }
                    $links_value[$_link->getLinkId()]['title'] = $_link->getLinkTitle();
                    $links_value[$_link->getLinkId()]['no_of_down'] = '(' . $_link->getNumberOfDownloadsUsed() . ' / ' . ($_link->getNumberOfDownloadsBought() ? $_link->getNumberOfDownloadsBought() : 'U') . ')';
                    $links_value[$_link->getLinkId()]['status'] = $_link->getStatus();
                    $links_value[$_link->getLinkId()]['downloadable_link'] = $finalLink;
                }

                $info = array(
                    'links' => $links_value
                );
            }

            $result = array();
            if ($options = $item->getProductOptions()) {
                if (isset($options['options'])) {
                    $result = array_merge($result, $options['options']);
                }
                if (isset($options['additional_options'])) {
                    $result = array_merge($result, $options['additional_options']);
                }
                if (!empty($options['attributes_info'])) {
                    $result = array_merge($options['attributes_info'], $result);
                }
            }
            $info = array();
            if ($result) {
                foreach ($result as $_option) {
                    $info[] = array(
                        'label' => $_option['label'],
                        'value' => $_option['value']
                    );
                }
            }

            $skus = '';
            $product = $this->_productFactory->create()->load($item->getProductId());
            if ($item->getParentItem()) {
                continue;
            }
            if ($_options = $this->_getItemOptions($item)) {
                $skus = $_options;
            }
            $productId = $item['product_id'];
            $images = array();
            $productData = $this->_productFactory->create()->load($productId);
            $allMediaGalleryImages = $productData->getMediaGalleryImages();
            if ($allMediaGalleryImages) {
                foreach ($allMediaGalleryImages as $mediaImages) {
                    $images = $this->_imageHelper->init($productData, 'product_page_image_large')
                        ->setImageFile($mediaImages->getFile())->constrainOnly(false)->keepAspectRatio(true)->keepFrame(true)->resize(300, 330)
                        ->getUrl();
                }
            }
            $productsDetail[] = array(
                'product_id' => $item->getProductId(),
                'name' => $item->getName(),
                'sku' => $item->getSku(),
                'unit_price' => $this->_cartHelper->orderFormattedPrice($item->getOriginalPrice(), $order->getOrderCurrencyCode()),
                'ordered_qty' => round($item->getQtyOrdered(), 2),
                'row_total' => $this->_cartHelper->orderFormattedPrice($item->getRowTotal(), $order->getOrderCurrencyCode()),
                'options' => $skus ? $skus : '',
                'image' => $images,
                'attribute_info' => $info ? $info : ''
            );
            unset($info);
        }
        return $productsDetail;
    }
    private function _getItemOptions($item)
    {
        $id = array('id' => $item->getItemId());
        $order_items = $this->_orderItemModel->getCollection()->addFieldToFilter('parent_item_id', array('eq' => $id));
        $skus = array();
        foreach ($order_items as $order_item) {
            $product_data = $this->_productModel->load($order_item->getProductId());
            $skus[] = $product_data->getSku();
        }
        return $skus;
    }
    private function getTrackingInformation($orderId)
    {
        $trackingDetails = array();
        $order = $this->_orderModel->load($orderId);
        $tracksCollection = $order->getTracksCollection();
        foreach ($tracksCollection->getItems() as $track) {
            $trackingDetails[] = array(
                'track_number' => $track->getTrackNumber(),
                'title' => $track->getTitle(),
                'carrier_code' => $track->getCarrierCode()
            );
        }
        return $trackingDetails;
    }
}
