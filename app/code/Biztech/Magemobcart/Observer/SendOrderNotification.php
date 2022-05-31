<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Observer;

class SendOrderNotification implements \Magento\Framework\Event\ObserverInterface
{
    protected $scopeConfig;
    protected $cartHelper;
    protected $request;
    protected $zend;
    protected $orderconfigModel;
    protected $notificationHistoryModel;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Zend\Json\Json $zend,
        \Biztech\Magemobcart\Model\System\Config\Orderstatusmessage $orderconfigModel,
        \Biztech\Magemobcart\Model\Notificationhistory $notificationHistoryModel
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_zend = $zend;
        $this->_orderconfigModel = $orderconfigModel;
        $this->_notificationHistoryModel = $notificationHistoryModel;
    }

    /**
     * This function is used for check key is valid or not
     * @param  \Magento\Framework\Event\Observer $observer
     * @return Bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order_status = $this->_scopeConfig->getValue('magemobcart/notification/order_status');
        if ($order_status == 1) {
            $data = array();

            $order = $observer->getOrder();
            $data['entity_id'] = $order->getId();
            $data['title'] = $order->getIncrementId();
            $data['increment_id'] = $order->getIncrementId();
            $data['order_status'] = $order->getStatus();
            $orderStatus = $order->getStatus();
            $data['website_id'] = $order->getStoreId();
            $data['customer_email'] = $order->getCustomerEmail();
            $data['customer_id'] = $order->getCustomerId();
            $data['order_grandtotal'] = $order->getGrandtotal();
            $data['status'] = $this->_scopeConfig->getValue('magemobcart/notification/status');
            $statuses = explode(",", $data['status']);
            $data['os'] = $this->_scopeConfig->getValue('magemobcart/notification/choose_os');
            $data['order_status_msg'] = $this->_orderconfigModel->toOptionArray();
            $data['message'] = $data['order_status_msg'][$orderStatus];
            $data['state'] = $order->getState();
            $data['flag'] = 1;
            
            if (in_array($orderStatus, $statuses)) {
                $this->_cartHelper->sendNotification($data);
            }
            $this->addNoticationHistory($data);
        }
    }
    public function addNoticationHistory($data)
    {
        try {
            $notificationHistoryData = array();
            $notificationHistoryData['type'] = 'order';
            $notificationHistoryData['order_id'] = $data['entity_id'];
            $notificationHistoryData['customer_id'] = $data['customer_id'];
            $notificationHistoryData['is_read'] = 0;
            $notificationHistoryData['order_increment_id'] = $data['increment_id'];
            $notificationHistoryData['order_status'] = $data['order_status'];
            $notificationHistoryData['order_message'] = $data['message'];
            $notificationHistoryData['order_grandtotal'] = $data['order_grandtotal'];
            $notiModel = $this->_notificationHistoryModel->setData($notificationHistoryData);
            $notiModel->save();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
