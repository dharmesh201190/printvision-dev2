<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Savepaymentmobile extends \Magento\Framework\App\Action\Action
{
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
    protected $onepageCheckout;
    protected $_apiTypeFactory;
    protected $_apiType = \Magento\Paypal\Model\Api\Nvp::class;
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
     * @param \Magento\Checkout\Model\Type\Onepage               $onepageCheckout
     * @param \Magento\Paypal\Model\Api\Type\Factory             $apiTypeFactory
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
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        \Magento\Paypal\Model\Api\Type\Factory $apiTypeFactory,
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
        $this->_onepageCheckout = $onepageCheckout;
        $this->_apiTypeFactory = $apiTypeFactory;
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
                $paymentMethod = json_decode($postData['payment'], true);
                $storeid = $postData['storeid'];
                try {
                    $result = array();
                    $data = $paymentMethod;
                    $result = $this->getOnepage()->savePayment($data);
                    $this->_cartSession->getQuote()->save();
                    $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                    if ($this->getOnepage()->getQuote()->getIsVirtual()) {
                        if ($this->_config->getValue('requireBillingAddress')
                            == PaypalConfig::REQUIRE_BILLING_ADDRESS_VIRTUAL
                        ) {
                        }
                    } else {
                        $address = $this->getOnepage()->getQuote()->getShippingAddress();
                        $isOverridden = 0;
                        if (true === $address->validate()) {
                            $isOverridden = 1;
                        }
                        $dataArray = array(
                            "paypal_express_checkout_shipping_overridden" => 1
                        );
                        $this->getOnepage()->getQuote()->getPayment()->setAdditionalInformation(
                            $dataArray
                        );
                        $this->getOnepage()->getQuote()->getPayment()->save();
                    }
                    if (empty($result['error'])) {
                        $totals = $this->_cartSession->getQuote()->getTotals();
                        $items = $this->cart->getQuote()->getAllVisibleItems();
                        $result = $this->_cartHelper->getCartData($items, $totals, 'get_total', 'save_payment', $storeid);
                        return $result;
                    }
                } catch (\Exception $e) {
                    $result['status'] = 'error';
                    $result['message'] = $e->getMessage();
                    $jsonResult->setData($result);
                    return $jsonResult;
                }
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
    public function getOnepage()
    {
        return $this->_onepageCheckout;
    }
}
