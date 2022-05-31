<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Getpaymentmethods extends \Magento\Framework\App\Action\Action
{
    const XML_CHECKMO_TO = 'payment/checkmo/payable_to';
    const XML_CHECKMO_ADDRESS = 'payment/checkmo/mailing_address';
    const XML_CASHON_DELIVERY = 'payment/cashondelivery/instructions';
    const XML_CCSAVE_TYPE = 'payment/ccsave/cctypes';
    const XML_CCSAVE_CVV = 'payment/ccsave/useccv';
    const XML_BANK_TRANSFER = 'payment/banktransfer/instructions';

    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $deviceDataModel;
    protected $resourceConnection;
    protected $cart;
    protected $cartSession;
    protected $scopeConfig;
    protected $shipconfig;
    protected $paymentHelper;
    protected $paymentConfig;
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
     * @param CustomerCart                                       $cart
     * @param \Magento\Payment\Helper\Data                       $paymentHelper
     * @param \Magento\Payment\Model\Config                      $paymentConfig
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
        CustomerCart $cart,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_deviceDataModel = $deviceDataModel;
        $this->_resourceConnection = $resourceConnection;
        $this->cart = $cart;
        $this->_cartSession = $cartSession;
        $this->shipconfig=$shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_paymentConfig = $paymentConfig;
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
            try {
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                $quote = $this->_getQuote();
                $store = $quote ? $quote->getStoreId() : null;
                $paymentMethodsArray = array();
                $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
                $address = $quote->getShippingAddress();
                $methods = $this->_paymentHelper->getStoreMethods($store, $quote);
                
                if (count($this->_paymentHelper->getStoreMethods($store, $quote)) > 0) {
                    foreach ($this->_paymentHelper->getStoreMethods($store, $quote) as $method) {
                        $this->_assignMethod($method);

                        $html = array();
                        $html['instructions'] = null;

                        if ($method->getTitle()) {
                            $paymentMethodsArray[] = array(
                                    'code' => $method->getCode(),
                                    'title' => $method->getTitle(),
                                    'html' => $html
                                );
                        }
                    }
                } else {
                    $paymentMethodsArray['message'] = 'There is no payment available for this address.';
                }
                if (empty($paymentMethodsArray)) {
                    $paymentMethodsArray['message'] = 'There is no payment available for this address.';
                }
                $jsonResult->setData($paymentMethodsArray);
                return $jsonResult;
            } catch (\Exception $e) {
                $paymentMethodsArray = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function _getCart()
    {
        return $this->cart;
    }
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
    protected function _canUseMethod($method)
    {
        if (!$method->canUseForCountry($this->_getQuote()->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($this->_getQuote()->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        $total = $this->_getQuote()->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if ((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
    protected function _assignMethod($method)
    {
        $method->setInfoInstance($this->_getQuote()->getPayment());
        return $this;
    }
}
