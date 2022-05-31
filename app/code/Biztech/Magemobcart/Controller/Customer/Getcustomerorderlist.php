<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Getcustomerorderlist extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $orderGridCollection;
    protected $productModel;
    protected $orderModel;
    protected $shippingHelper;
    protected $date;
    protected $timezone;
    protected $imageHelper;
    protected $formKey;
    
    /**
     * @param Context                                                  $context
     * @param JsonFactory                                              $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data                         $cartHelper
     * @param \Magento\Framework\App\Request\Http                      $request
     * @param \Magento\Customer\Model\Session                          $customerSession
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $orderGridCollection
     * @param \Magento\Catalog\Model\ProductFactory                    $productModel
     * @param \Magento\Sales\Model\Order                               $orderModel
     * @param \Magento\Shipping\Helper\Data                            $shippingHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime              $date
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface     $timezone
     * @param \Magento\Catalog\Helper\Image                            $imageHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $orderGridCollection,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Shipping\Helper\Data $shippingHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_orderGridCollection = $orderGridCollection;
        $this->_productModel = $productModel;
        $this->_orderModel = $orderModel;
        $this->_shippingHelper = $shippingHelper;
        $this->_date = $date;
        $this->_timezone = $timezone;
        $this->_imageHelper = $imageHelper;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get all orderlist of customer.
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
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customerId = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customerId[0]);
                }
            }
            $customerId = $postData['customer_id'];
            $limit = $postData['limit'];
            $storeId = $postData['storeid'];
            $offset = $postData['offset'];
            $orderListData = array();

            $orderCollection = $this->_orderGridCollection->addFieldToFilter('customer_id', array('eq' => $customerId))->addFieldToFilter('store_id', array('eq' => $storeId))->setOrder('entity_id', 'desc');
            $orderCollection->getSelect()->limit($limit);
            if ($offset != null && $offset != 0) {
                $orderCollection->addFieldToFilter('entity_id', array('lt' => $offset));
            }
            $orderCollection->setPageSize($limit);
            foreach ($orderCollection as $order) {
                $orderModelData = $this->_orderModel->load($order->getEntityId());
                $images = array();

                if (count($orderModelData->getItemsCollection()->getData()) > 1) {
                    $ordername = 'Order has multiple items.';
                } else {
                    $ordernameCollection = $orderModelData->getItemsCollection()->getData();
                    $ordername = $ordernameCollection[0]['name'];
                }
                foreach ($orderModelData->getAllItems() as $valueOrder) {
                    $productId = $valueOrder['product_id'];
                    $productData = $this->_productModel->create()->load($productId);
                    $allMediaGalleryImages = $productData->getMediaGalleryImages();
                    if ($allMediaGalleryImages) {
                        foreach ($allMediaGalleryImages as $mediaImages) {
                            $images[] = $this->_imageHelper->init($productData, 'product_page_image_large')
                                ->setImageFile($mediaImages->getFile())->constrainOnly(false)->keepAspectRatio(true)->keepFrame(true)->resize(300, 330)
                                ->getUrl();
                        }
                    }
                }


                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $OrderModel = $objectManager->create('Magento\Sales\Model\Order')->load($order->getEntityId());

                $trackingUrl = '';
                if ($OrderModel->getTracksCollection()->count()) {
                    $trackingUrl = $this->_shippingHelper->getTrackingPopupUrlBySalesModel($OrderModel);
                }
                $orderListData[] = array(
                    'order_name' => $ordername,
                    'entity_id' => $order->getEntityId(),
                    'increment_id' => $order->getIncrementId(),
                    'store_id' => $order->getStoreId(),
                    'customer_name' => $order->getBillingName(),
                    'status' => $order->getStatus(),
                    'order_date' => $this->_timezone->date(new \DateTime($order->getCreatedAt()))->format('Y-m-d H:i:s'),
                    'grand_total' => $this->_cartHelper->orderFormattedPrice($order->getGrandTotal(), $order->getOrderCurrencyCode()),
                    'toal_qty' => round($OrderModel->getTotalQtyOrdered(), 2),
                    'product_images' => $images,
                    'tracking_url' => $trackingUrl
                );
            }
            $updatedTime = $this->_date->gmtDate();
            $orderListResultArr = array('orderlistdata' => $orderListData, 'updated_time' => $updatedTime);
            $jsonResult->setData($orderListResultArr);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
