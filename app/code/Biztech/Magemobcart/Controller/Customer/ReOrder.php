<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class ReOrder extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $orderModel;
    protected $registry;
    protected $cartModel;
    protected $cartSession;
    protected $orderConfigModel;
    protected $formKey;
    
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Sales\Model\Order          $orderModel
     * @param \Magento\Framework\Registry         $registry
     * @param \Magento\Checkout\Model\Cart        $cartModel
     * @param \Magento\Checkout\Model\Session     $cartSession
     * @param \Magento\Sales\Model\Order\Config   $orderConfigModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Checkout\Model\Session $cartSession,
        \Magento\Sales\Model\Order\Config $orderConfigModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_orderModel = $orderModel;
        $this->_registry = $registry;
        $this->_cartModel = $cartModel;
        $this->_cartSession = $cartSession;
        $this->_orderConfigModel = $orderConfigModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for reorder from existing order.
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
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            $orderId = $postData['order_id'];
            if (array_key_exists('order_id', $postData) && array_key_exists('customer_id', $postData)) {
                $customerId = $postData['customer_id'];
                $orderId = $postData['order_id'];
                if ($customerId != "" && $orderId != "") {
                    if (!$this->_loadValidOrder()) {
                        $returnExtensionArray = array('status' => false,'message' => 'Order is not valid');
                        $jsonResult->setData($returnExtensionArray);
                        return $jsonResult;
                    }
                    $order = $this->_registry->registry('current_order');
                    $cart = $this->_cartModel;
                    $cartTruncated = false;

                    $orderVisibleItems = $order->getAllVisibleItems();
                    foreach ($orderVisibleItems as $orderItem) {
                        try {
                            $cart->addOrderItem($orderItem);
                            $responseArr[] = array('status' => 'success', 'message' => 'Items added to cart successfully', 'name' => $orderItem->getName(), 'id' => $orderItem->getId());
                        } catch (\Exception $e) {
                            if ($this->_cartSession->getUseNotice(true)) {
                                $responseArr[] = array('status' => 'notice', 'message' => $e->getMessage(), 'name' => $orderItem->getName(), 'id' => $orderItem->getId());
                            } else {
                                $responseArr[] = array('status' => 'error', 'message' => $e->getMessage(), 'name' => $orderItem->getName(), 'id' => $orderItem->getId());
                            }
                        } catch (\Exception $e) {
                            $responseArr[] = array('status' => 'error', 'message' => $e->getMessage(), 'name' => $orderItem->getName(), 'id' => $orderItem->getId());
                        }
                    }
                    $noticeArray = array();
                    $errorArray = array();
                    $successArray = array();

                    foreach ($responseArr as $key => $value) {
                        if ($value['status'] == 'notice') {
                            $noticeArray[$value['id']] = $value['name'];
                        }

                        if ($value['status'] == 'error') {
                            $errorArray[$value['id']] = $value['name'];
                        }

                        if ($value['status'] == 'success') {
                            $successArray[$value['id']] = $value['name'];
                        }
                    }
                    $tags = implode(', ', $noticeArray);
                    $tags1 = implode(', ', $errorArray);
                    $tags2 = implode(', ', $successArray);
                    $finalNoticeArray = array('status' => 'notice', 'name' => $tags);
                    $finalerrorArray = array('status' => 'error', 'name' => $tags1);
                    $finalsuccessArray = array('status' => 'success', 'name' => $tags2);
                    $i = array();
                    if (isset($tags) && $tags != "") {
                        $i[] = $finalNoticeArray;
                    }
                    if (isset($tags1) && $tags1 != "") {
                        $i[] = $finalerrorArray;
                    }
                    if (isset($tags2) && $tags2 != "") {
                        $i[] = $finalsuccessArray;
                    }
                    $cart->save();
                    $jsonResult->setData($i);
                    return $jsonResult;
                } else {
                    $returnExtensionArray = array('status' => false,'message' => 'Order or Custoemr should not blank');
                    $jsonResult->setData($returnExtensionArray);
                    return $jsonResult;
                }
            } else {
                $returnExtensionArray = array('status' => false,'message' => 'Order or Custoemr is missing');
                $jsonResult->setData($returnExtensionArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    /**
     * Check existing order is valid or not
     * @param  int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->_request->getParam('order_id');
            $customerId = (int) $this->_request->getParam('customer_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = $this->_orderModel->load($orderId);

        if ($this->_canViewOrder($order, $customerId)) {
            $this->_registry->register('current_order', $order);
            return true;
        } else {
            return false;
        }
        return false;
    }

    /**
     * [_canViewOrder description]
     * @param  [type] $order
     * @param  [type] $customerId
     * @return [type]
     */
    protected function _canViewOrder($order, $customerId)
    {
        $availableStates = $this->_orderConfigModel->getVisibleOnFrontStatuses();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId) && in_array($order->getStatus(), $availableStates, $strict = true)
        ) {
            return true;
        }
        return false;
    }
}
