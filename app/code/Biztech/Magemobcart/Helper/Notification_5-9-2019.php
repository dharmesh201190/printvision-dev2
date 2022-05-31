<?php

namespace Biztech\Magemobcart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Notification extends AbstractHelper
{
    const XML_ANDROID_KEY = 'magemobcart/magemobcart_general/authorization_key';
    const XML_IOS_KEY = 'magemobcart/magemobcart_general/upload_notification_file';
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $notificationHistoryModel;
    protected $notificationModel;
    protected $deviceModel;
    protected $categoryModel;
    protected $scopeConfig;
    protected $messageManager;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel,
        \Biztech\Magemobcart\Model\Notification $notificationModel,
        \Biztech\Magemobcart\Model\Devicedata $deviceModel,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_notificationHistoryModel = $notificationHistoryModel;
        $this->_notificationModel = $notificationModel;
        $this->_deviceModel = $deviceModel;
        $this->_categoryModel = $categoryModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * This function is used for get all categories list with tree structure.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function getNotificationCount($customerId)
    {
        $jsonResult = $this->_jsonFactory->create();
        
        $deviceModelData = $this->_deviceModel->getCollection();
        $deviceModelData->addFieldToFilter('customer_id', $customerId);
        $os = "all";
        if (!empty($deviceModelData->getData())) {
            foreach ($deviceModelData->getData() as $key => $value) {
                $os = $value['device_type'];
            }
        }
        $igoneOfferIds = $this->getReadOffers();
        $notificationHistoryData = $this->_notificationHistoryModel->getCollection();
        $notificationHistoryData->addFieldToFilter('customer_id', $customerId);
        $notificationHistoryData->addFieldToFilter('type', 'order');
        $notificationHistoryData->addFieldToFilter('is_read', 0);
        if (count($notificationHistoryData) != 0) {
            $count = 1;
        } else {
            $count = 0;
        }
        return $count;
        $offerNotificationData = $this->_notificationModel->getCollection();
        if (!empty($igoneOfferIds)) {
            $offerNotificationData->addFieldToFilter('notification_id', array('nin' => $igoneOfferIds));
        }
        $allNotificationData = array();
        foreach ($notificationHistoryData->getData() as $key => $history) {
            $allNotificationData[] = array(
            'notification_history_id' => $history['notification_history_id'],
            'type' => $history['type'],
            'order_id' => $history['order_id'],
            'customer_id' => $history['customer_id'],
            'order_increment_id' => $history['order_increment_id'],
            'order_status' => $history['order_status'],
            'message' => $history['order_message'],
            'order_grandtotal' => $history['order_grandtotal'],
            'created_at' => $history['created_at'],
            'offer_id' => '',
            'title' => '',
            'url' => '',
            'category_id' => '',
            'product_id' => '',
            'order_status' => '',
            'filepath' => '',
            'category_name' => '',
            'is_read' => $history['is_read']
            );
        }
        foreach ($offerNotificationData->getData() as $key => $offer) {
            if (isset($offer['category_id'])) {
                $categoryName = $this->_categoryModel->load($offer['category_id'])->getName();
            }
            $allNotificationData[] = array(
            'notification_history_id' => '',
            'type' => $offer['type'],
            'order_id' => '',
            'customer_id' => '',
            'order_increment_id' => '',
            'order_status' => '',
            'order_grandtotal' => '',
            'offer_id' => $offer['notification_id'],
            'title' => $offer['title'],
            'url' => $offer['url'],
            'message' => $offer['message'],
            'category_id' => $offer['category_id'],
            'product_id' => $offer['product_id'],
            'order_status' => $offer['order_status'],
            'filepath' => $offer['filepath'],
            'created_at' => $offer['start_date'],
            'category_name' => $categoryName,
            'is_read' => $this->getOfferReadId($offer['notification_id'])
            );
        }
        return count($allNotificationData);
    }

    public function getReadOffers()
    {
        $offerNotificationData = $this->_notificationHistoryModel->getCollection();
        $offerNotificationData->addFieldToFilter('is_read', array('nin' => 1,2));
        // $offerNotificationData->addFieldToFilter('is_read', array('eq' => 1));
        $offerIds = array();
        foreach ($offerNotificationData->getData() as $key => $value) {
            $offerIds[] = $value['offer_id'];
        }
        return $offerIds;
    }
    public function getOfferReadId($offerId)
    {
        $offerNotificationData = $this->_notificationHistoryModel->getCollection();
        $offerIds = '';
        foreach ($offerNotificationData->getData() as $key => $value) {
            $offerIds = $value['is_read'];
        }
        return $offerIds;
    }
    public function sendNotification($data)
    {
        $result = $this->send($data);
        return $result;
    }
    public function send($data)
    {
        $website = $data['website_id'];
        $collectionDevice = $this->_deviceModel->getCollection();
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
        $collectionDeviceios = $this->_deviceModel->getCollection()
        ->addFieldToFilter('device_type', 'ios');
        // ->addFieldToFilter('device_token', 'fGmBwdvlrug:APA91bE7OLPU-YfYIbFi9Nsr0qF1qHjDwt5rlGSt06XTeOZ4kTScLm9YYh0Zw4nGBvqu_4LlMMeu8gFrK5pxQ-fqolv64fnMID9UmJ_yD5YtrlqLcJFjH19EpvUGHnqaeuoHgEOclXfo');

        // ->addFieldToFilter('device_id', array('neq'=> null));
        if (!array_key_exists("flag", $data)) {
            $collectionDeviceios->addFieldToFilter('cron_status', 'pending');
        }
        $collectionDeviceios->getSelect()->group('device_id');
        $deviceModel = $this->_deviceModel;
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
            if (isset($data['flag']) && $data['flag'] == 1) {
            }
        }
        if (array_key_exists('category_id', $data)) {
            if (!empty($data['category_id']) && $data['category_id'] > 0) {
                $_category = $this->_categoryModel->load($data['category_id']);
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
            'Content-Type: application/json');
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
        $this->_messageManager->addSuccess(__("Message successfully delivered (IOS)"));
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
                    'Authorization:key='.$iosKey
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
        $collectionDeviceandroid = $this->_deviceModel->getCollection()
        ->addFieldToFilter('device_type', 'android');
        if (!array_key_exists("flag", $data)) {
            $collectionDeviceandroid->addFieldToFilter('cron_status', 'pending');
        }
        $deviceModel = $this->_deviceModel;
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
                $_category = $this->_categoryModel->load($data['category_id']);
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
        $url = 'https://android.googleapis.com/gcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json');
        
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
        if ($re == "") {
            $this->_messageManager->addError(__("Message not delivered (Android)"));
            return false;
        }
        $this->_messageManager->addSuccess(__("Message successfully delivered (Android)"));
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
        } catch (\Exception $e) {
        }
        return $result;
    }
}
