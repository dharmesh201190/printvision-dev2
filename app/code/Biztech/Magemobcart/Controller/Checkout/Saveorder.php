<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Saveorder extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $deviceDataModel;
    protected $resourceConnection;
    protected $cartSession;
    protected $scopeConfig;
    protected $shipconfig;
    protected $paymentHelper;
    protected $onepageCheckout;
    protected $customerModel;
    protected $formKey;

    /**
     * @param Context                                            $context
     * @param JsonFactory                                        $jsonFactory
     * @param \Magento\Framework\App\Request\Http                $request
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Biztech\Magemobcart\Model\Devicedata              $deviceDataModel
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Checkout\Model\Session                    $cartSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config                     $shipconfig
     * @param \Magento\Payment\Helper\Data                       $paymentHelper
     * @param \Magento\Checkout\Model\Type\Onepage               $onepageCheckout
     * @param \Magento\Customer\Api\CustomerRepositoryInterface  $customerModel
     * @param \Magento\Framework\Data\Form\FormKey               $formKey
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Biztech\Magemobcart\Model\Devicedata $deviceDataModel,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Checkout\Model\Session $cartSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_deviceDataModel = $deviceDataModel;
        $this->_resourceConnection = $resourceConnection;
        $this->_cartSession = $cartSession;
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_onepageCheckout = $onepageCheckout;
        $this->_customerModel = $customerModel;
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
            $session = $this->_customerSession;
            $responseArr['status'] = 'false';
            $postData = $this->_request->getParams();
            $storeId = $postData['storeid'];
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }

            $paymentMethod = json_decode($postData['payment'], true);
            $result = array();
            $result['status'] = 'error';

            $this->getQuote()->setStoreId($storeId);
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getQuote()->getPayment()->importData($paymentMethod);
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quoteManagement = $objectManager->get('Magento\Quote\Api\CartManagementInterface');
            $order = $quoteManagement->submit($this->getQuote());
            $eventManager = $objectManager->get('Magento\Framework\Event\ManagerInterface');
            $this->_cartSession
                ->setLastQuoteId($this->getQuote()->getId())
                ->setLastSuccessQuoteId($this->getQuote()->getId())
                ->clearHelperData();

            if ($order) {
                $eventManager->dispatch(
                    'checkout_type_onepage_save_order_after',
                    ['order' => $order, 'quote' => $this->getQuote()]
                );
                $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();

                $this->_cartSession
                    ->setLastOrderId($order->getId())
                    ->setRedirectUrl($redirectUrl)
                    ->setLastRealOrderId($order->getIncrementId())
                    ->setLastOrderStatus($order->getStatus());

                $checkout_data = $this->_cartSession->getData();

                $result['last_success_quote_id'] = $checkout_data['last_success_quote_id'];
                $result['last_quote_id'] = $checkout_data['last_quote_id'];
                $result['last_order_id'] = $checkout_data['last_order_id'];
                $result['increment_id'] = $checkout_data['last_real_order_id'];
            }

            $eventManager->dispatch(
                'checkout_submit_all_after',
                [
                    'order' => $order,
                    'quote' => $this->getQuote()
                ]
            );
            $result['status'] = 'success';
            $jsonResult->setData($result);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    public function getOnepage()
    {
        return $this->_onepageCheckout;
    }
    public function getQuote()
    {
        return $this->_cartSession->getQuote();
    }
}
