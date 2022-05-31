<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Paypalurl extends \Magento\Framework\App\Action\Action
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
        try {
            $jsonResult = $this->_jsonFactory->create();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $redirectLink = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            $redirect = $objectManager->create('Magento\Framework\Controller\Result\Redirect');
            $redirect->setUrl($redirectLink);
            return $redirect;
        } catch (\Exception $e) {
            $jsonResult->setUrl(array("status" => false,"message" => $e->getMessage()));
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
