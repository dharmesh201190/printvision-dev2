<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;

class Success extends \Magento\Framework\App\Action\Action
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
    protected $eventManager;
    protected $formKey;

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
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_deviceDataModel = $deviceDataModel;
        $this->_resourceConnection = $resourceConnection;
        $this->_cartSession = $cartSession;
        $this->shipconfig=$shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_onepageCheckout = $onepageCheckout;
        $this->_customerModel = $customerModel;
        $this->_eventManager = $eventManager;
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
        $result['status'] = 'error';
        $responseArr['status'] = 'false';
        $postData = $this->_request->getParams();
        $storeId = $postData['storeid'];
        $sessionId = '';
        $session = $this->getOnepage()->getCheckout();
        if (isset($postData['session_id']) && $postData['session_id'] != null) {
            $sessionId = $postData['session_id'];
            if (!$this->_customerSession->isLoggedIn()) {
                $customer_id = explode("_", $sessionId);
                $this->_cartHelper->relogin($customer_id[0]);
            }
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$session->getLastSuccessQuoteId()) {
            if ($postData['last_success_quote_id'] == null) {
                $result['status'] = 'error';
                $result['redirect'] = 'cart';
                $jsonResult->setData($result);
                return $jsonResult;
            }
        }
        if (!$session->getLastQuoteId() || $postData['last_quote_id'] != null) {
            $lastQuoteId = $postData['last_quote_id'];
        } else {
            $lastQuoteId = $session->getLastQuoteId();
        }

        if (!$session->getLastOrderId() || $postData['last_order_id'] != null) {
            $lastOrderId = $postData['last_order_id'];
        } else {
            $lastOrderId = $session->getLastOrderId();
        }
        $session->clearQuote();
        $this->_eventManager->dispatch(
            'checkout_onepage_controller_success_action',
            ['order_ids' => [$session->getLastOrderId()]]
        );
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($lastOrderId);
        $orderIncId = $order->getIncrementId();
        $entityId = $order->getEntityId();
        $result['increment_id'] = $orderIncId;
        $result['entity_id'] = $entityId;
        $result['status'] = 'success';
        $result['message'] = 'Your order has been successfully placed.';
        $jsonResult->setData($result);
        return $jsonResult;
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
