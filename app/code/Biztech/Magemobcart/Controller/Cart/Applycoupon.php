<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Cart;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class Applycoupon extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Checkout\Model\Session     $checkoutSession
     * @param CustomerCart                        $customerCart
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        CustomerCart $customerCart,
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
     * This function is used for apply and remove coupon code
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
            if (isset($params['session_id']) && $params['session_id'] != null) {
                $sessionId = $params['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            try {
                $productDetailResult = array();
                $couponCode = $postData['coupon_code'];
                $action = $postData['remove'];

                if ($action == 1) {
                    $couponCode = '';
                }

                $oldCouponCode = $this->_customerCart->getQuote()->getCouponCode();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $coupon = $objectManager->get('Magento\SalesRule\Model\CouponFactory')->create();
                $coupon->load($couponCode, 'code');

                if ($couponCode && $coupon->getId()) {
                    $productDetailResult = array(
                        'status' => 'false',
                        'message' => 'Coupon code ' . $couponCode . ' was not applied.',
                        'coupon_code' => $couponCode
                    );
                    $jsonResult->setData($productDetailResult);
                    return $jsonResult;
                }
                if (strlen($couponCode)) {
                    if ($couponCode != $this->_customerCart->getQuote()->getCouponCode()) {
                        $this->_checkoutSession->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
                        $cart = $this->_customerCart;
                        $totals = $this->_checkoutSession->getQuote()->getTotals();
                        $items = $this->_customerCart->getQuote()->getAllVisibleItems();
                        $cartData = $this->_cartHelper->getCartData($items, $totals, 'coupon', '', $storeId);
                    } else {
                        $productDetailResult = array(
                            'status' => 'false',
                            'message' => 'Coupon code ' . $couponCode . ' is not valid.',
                            'coupon_code' => $couponCode
                        );
                        $jsonResult->setData($productDetailResult);
                        return $jsonResult;
                    }
                } else {
                    $cart = $this->_customerCart;
                    $this->_customerCart->getQuote()->setCouponCode('')->collectTotals()->save();
                    $totals = $this->_checkoutSession->getQuote()->getTotals();
                    $items = $this->_customerCart->getQuote()->getAllVisibleItems();
                    $cartData = $this->_cartHelper->getCartData($items, $totals, 'coupon_remove', '', $storeId);
                }
                return $cartData;
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
}
