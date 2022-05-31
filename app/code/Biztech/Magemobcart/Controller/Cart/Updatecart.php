<?php
/**
 *
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Cart;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class Updatecart extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $checkoutSession;
    protected $customerCart;
    protected $formKey;

    /**
     * @param Context                             $context
     * @param JsonFactory                         $jsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Biztech\Magemobcart\Helper\Data    $cartHelper
     * @param \Magento\Customer\Model\Session     $customerSession
     * @param CustomerCart                        $customerCart
     * @param \Magento\Checkout\Model\Session     $checkoutSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        CustomerCart $customerCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerCart = $customerCart;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for update the qty and remove cart all items
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
            $params = $this->_request->getParams();
            $storeId = $params['storeid'];
            $sessionId = '';
            if (isset($params['session_id']) && $params['session_id'] != null) {
                $sessionId = $params['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            try {
                $updateAction = $params['update_cart_action'];
                if (array_key_exists('cart_data', $params)) {
                    $cartData = (string) $params['cart_data'];
                    $params['cart'] = json_decode($cartData, true);
                }
                switch ($updateAction) {
                    case 'empty_cart':
                        $cartData1 = $this->_emptyShoppingCart($params, $storeId);
                        break;
                    case 'update_qty':
                        $cartData1 = $this->_updateShoppingCart($params, $storeId);
                        break;
                    default:
                        $cartData1 = $this->_updateShoppingCart($params, $storeId);
                }
                return $cartData1;
            } catch (\Exception $e) {
                $productDetailResult = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
                $jsonResult->setData($productDetailResult);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    protected function _emptyShoppingCart($params, $storeId)
    {
        $jsonResult = $this->_jsonFactory->create();
        $cart = $this->_customerCart;
        try {
            $this->_customerCart->truncate();
            $this->_customerCart->saveQuote();
        } catch (\Exception $exception) {
            $result = array(
                'status' => 'false',
                'message' => $exception->getMessage()
            );
            $jsonResult->setData($result);
            return $jsonResult;
        }
        $totals = $this->_checkoutSession->getQuote()->getTotals();
        $items = $this->_customerCart->getQuote()->getAllVisibleItems();
        $cartData = $this->_cartHelper->getCartData($items, $totals, 'update', '', $storeId);
        return $cartData;
    }
    protected function _updateShoppingCart($params, $storeId)
    {
        $jsonResult = $this->_jsonFactory->create();
        try {
            $cartData = $params['cart'];
            if (is_array($cartData)) {
                foreach ($cartData as $index => $data) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get(
                            \Magento\Framework\Locale\ResolverInterface::class
                        )->getLocale()]
                    );
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_customerCart;
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $cart->updateItems($cartData)->save();
            }
            $this->_checkoutSession->setCartWasUpdated(true);
            $totals = $this->_checkoutSession->getQuote()->getTotals();
            $items = $this->_customerCart->getQuote()->getAllVisibleItems();
            $cartData1 = $this->_cartHelper->getCartData($items, $totals, 'update', '', $storeId);
            return $cartData1;
        } catch (\Exception $e) {
            $this->_checkoutSession->setCartWasUpdated(true);
            $totals = $this->_checkoutSession->getQuote()->getTotals();
            $items = $this->_customerCart->getQuote()->getAllVisibleItems();
            $cartData1 = $this->_cartHelper->getCartData($items, $totals, 'update', '', $storeId);
            return $cartData1;
        }
    }
}
