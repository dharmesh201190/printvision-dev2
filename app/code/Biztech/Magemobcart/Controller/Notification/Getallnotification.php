<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Notification;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Getallnotification extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $notificationHistoryModel;
    protected $notificationModel;
    protected $deviceModel;
    protected $categoryModel;
    protected $formKey;
    protected $orderModel;
    protected $productModel;
    protected $imageHelper;
    protected $date;

    /**
     * @param Context                                        $context
     * @param JsonFactory                                    $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data               $cartHelper
     * @param \Magento\Framework\App\Request\Http            $request
     * @param \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel
     * @param \Biztech\Magemobcart\Model\Notification        $notificationModel
     * @param \Biztech\Magemobcart\Model\Devicedata          $deviceModel
     * @param \Magento\Catalog\Model\Category                $categoryModel
     * @param \Magento\Sales\Model\Order                     $orderModel
     * @param \Magento\Catalog\Model\ProductFactory          $productModel
     * @param \Magento\Catalog\Helper\Image                  $imageHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime    $date
     * @param \Magento\Framework\Data\Form\FormKey           $formKey
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel,
        \Biztech\Magemobcart\Model\Notification $notificationModel,
        \Biztech\Magemobcart\Model\Devicedata $deviceModel,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_notificationHistoryModel = $notificationHistoryModel;
        $this->_notificationModel = $notificationModel;
        $this->_deviceModel = $deviceModel;
        $this->_categoryModel = $categoryModel;
        $this->_orderModel = $orderModel;
        $this->_productModel = $productModel;
        $this->_imageHelper = $imageHelper;
        $this->date = $date;
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
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            try {
                if (array_key_exists('customer_id', $postData)) {
                    $customerId = $postData['customer_id'];
                    $deviceModelData = $this->_deviceModel->getCollection();
                    $deviceModelData->addFieldToFilter('customer_id', $customerId);
                    
                    $os = "all";
                    if (!empty($deviceModelData->getData())) {
                        foreach ($deviceModelData->getData() as $key => $value) {
                            $os = $value['device_type'];
                        }
                    }
                    $notificationHistoryData = $this->_notificationHistoryModel->getCollection();
                    $notificationHistoryData->addFieldToFilter('customer_id', $customerId);
                    $notificationHistoryData->addFieldToFilter('type', 'order');
                    $notificationHistoryData->setOrder('created_at', 'DESC');
                    $offerNotificationData = $this->_notificationModel->getCollection();
                    
                    $allNotificationData = array();
                    foreach ($notificationHistoryData->getData() as $key => $history) {
                        $daylen = 60*60*24;
                        $date1 = $history['created_at'];
                        $date2 = $this->date->gmtDate();
                        $finalDate = round((strtotime($date2)-strtotime($date1))/$daylen)." Days ago";
                        $allNotificationData[] = array(
                        'notification_history_id' => $history['notification_history_id'],
                        'type' => $history['type'],
                        'order_id' => $history['order_id'],
                        'customer_id' => $history['customer_id'],
                        'order_increment_id' => $history['order_increment_id'],
                        'order_status' => $history['order_status'],
                        'message' => $history['order_message'],
                        'order_grandtotal' => $history['order_grandtotal'],
                        'created_at' => $finalDate,
                        'offer_id' => '',
                        'title' => '',
                        'url' => '',
                        'category_id' => '',
                        'product_id' => '',
                        'order_status' => '',
                        'filepath' => $this->getOrderImage($history['order_id']),
                        'category_name' => '',
                        'is_read' => $history['is_read']
                        );
                    }
                    $all = array('items' => $allNotificationData);
                    $jsonResult->setData($all);
                    return $jsonResult;
                } else {
                    $customer_array = array(
                    'status' => 'error',
                    'message' => 'Customer Id is missing'
                    );
                    $jsonResult->setData($customer_array);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $customer_array = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
                $jsonResult->setData($customer_array);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    public function getOrderImage($orderId)
    {
        $orderModelData = $this->_orderModel->load($orderId);
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
        return $images[0];
    }
}
