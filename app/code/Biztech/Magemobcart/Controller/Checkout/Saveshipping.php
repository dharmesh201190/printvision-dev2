<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Saveshipping extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $customerModel;
    protected $deviceDataModel;
    protected $resourceConnection;
    protected $cart;
    protected $cartSession;
    protected $scopeConfig;
    protected $shipconfig;
    protected $paymentHelper;
    protected $onepageCheckout;
    protected $formKey;

    /**
     * @param Context                                            $context
     * @param JsonFactory                                        $jsonFactory
     * @param \Magento\Framework\App\Request\Http                $request
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Customer\Model\Customer                   $customerModel
     * @param \Biztech\Magemobcart\Model\Devicedata              $deviceDataModel
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Checkout\Model\Session                    $cartSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config                     $shipconfig
     * @param CustomerCart                                       $cart
     * @param \Magento\Payment\Helper\Data                       $paymentHelper
     * @param \Magento\Checkout\Model\Type\Onepage               $onepageCheckout
     * @param \Magento\Framework\Data\Form\FormKey               $formKey
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Biztech\Magemobcart\Model\Devicedata $deviceDataModel,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Checkout\Model\Session $cartSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig,
        CustomerCart $cart,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_customerModel = $customerModel;
        $this->_deviceDataModel = $deviceDataModel;
        $this->_resourceConnection = $resourceConnection;
        $this->cart = $cart;
        $this->_cartSession = $cartSession;
        $this->shipconfig=$shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_onepageCheckout = $onepageCheckout;
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
                $result = array();
                $billing = json_decode($postData['shipping'], true);
                $storeid = $postData['storeid'];
                $data = $billing;
                $this->getQuote()->setStoreId($storeid);
                $customerAddressId = $data['address_id'];
                $this->getQuote()->setCustomerId($data['customer_id']);
                $customerData = $this->_customerModel->load($data['customer_id'])->getData();
                $this->getQuote()->setCustomer($customerData);

                

                if (isset($data['email'])) {
                    $data['email'] = trim($data['email']);
                }
                $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
                $this->cart->save();
                $totals = $this->_cartSession->getQuote()->getTotals();
                $items = $this->cart->getQuote()->getAllVisibleItems();
                $result = $this->_cartHelper->getCartData($items, $totals, 'get_total', '', $storeId);
                return $result;
            } catch (\Exception $e) {
                $billingAddressArray = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
                $jsonResult->setData($billingAddressArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
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
