<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Saveshippingmethod extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $deviceDataModel;
    protected $resourceConnection;
    protected $cartModel;
    protected $cartSession;
    protected $scopeConfig;
    protected $shipconfig;
    protected $onepageCheckout;
    protected $formKey;

    /**
     * @param Context                                            $context
     * @param JsonFactory                                        $jsonFactory
     * @param \Magento\Framework\App\Request\Http                $request
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Biztech\Magemobcart\Model\Devicedata              $deviceDataModel
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Checkout\Model\Cart                       $cartModel
     * @param \Magento\Checkout\Model\Session                    $cartSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config                     $shipconfig
     * @param \Magento\Checkout\Model\Type\Onepage               $onepageCheckout
     * @param CustomerCart                                       $cart
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
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Checkout\Model\Session $cartSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        CustomerCart $cart,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_deviceDataModel = $deviceDataModel;
        $this->_resourceConnection = $resourceConnection;
        $this->_cartModel = $cartModel;
        $this->_cartSession = $cartSession;
        $this->shipconfig=$shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->_onepageCheckout = $onepageCheckout;
        $this->cart = $cart;
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
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            $postData = $this->_request->getParams();
            $storeId = $postData['storeid'];
            $shippingMethod = $postData['shipping_method']."_".$postData['shipping_method'];
            $this->_cartSession->getQuote()->save();
            $shippingAddress = $this->_cartSession->getQuote()->getShippingAddress();
            $shippingAddress->setShippingMethod($shippingMethod)->collectShippingRates();
            $shippingAddress->save();
            $this->_cartSession->getQuote()->save();
            $this->getOnepage()->getQuote()->collectTotals();
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $totals = $this->_cartSession->getQuote()->getTotals();
            $items = $this->cart->getQuote()->getAllVisibleItems();
            $result = $this->_cartHelper->getCartData($items, $totals, 'get_total', 'save_shipping', $storeId);
            return $result;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function _getCart()
    {
        return $this->_cartModel;
    }
    public function getQuote()
    {
        return $this->_cartSession;
    }
    public function getOnepage()
    {
        return $this->_onepageCheckout;
    }
}
