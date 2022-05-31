<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class CancelOrder extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $orderModel;
    protected $eventManager;
    protected $formKey;
    
    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param \Magento\Sales\Model\Order          $orderModel
     * @param \Magento\Framework\Event\Manager    $eventManager
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_orderModel = $orderModel;
        $this->_eventManager = $eventManager;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for cancel order
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
            $order = $this->_orderModel->load($this->getRequest()->getParam('order_id'));
            if ($order->getId()) {
                if ($order->canCancel()) {
                    try {
                        $order->cancel();
                        $order->getPayment()->cancel();
                        $order->registerCancellation();
                        $order->save();
                        $this->_eventManager->dispatch('order_cancel_after', ['order' => $order]);
                        $responseArr = array('status' => 'success', 'message' => 'Your order has been canceled.');
                    } catch (\Exception $e) {
                        $responseArr = array('status' => 'error', 'message' => $e->getMessage());
                    }
                } else {
                    $responseArr = array('status' => 'error', 'message' => 'Cannot cancel your order.');
                }
                $jsonResult->setData($responseArr);
                return $jsonResult;
            }
            $jsonResult->setData($orderListResultArr);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
