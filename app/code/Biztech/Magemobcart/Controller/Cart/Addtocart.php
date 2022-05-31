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

class Addtocart extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $checkoutSession;
    protected $customerCart;
    protected $scopeConfig;
    protected $formKey;
    
    /**
     * @param Context                                            $context
     * @param JsonFactory                                        $jsonFactory
     * @param \Magento\Framework\App\Request\Http                $request
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param CustomerCart                                       $customerCart
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param ProductRepositoryInterface                         $productRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        CustomerCart $customerCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->_customerCart = $customerCart;
        $this->_scopeConfig = $scopeConfig;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for add to cart
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
            $productId = $params['product'];
            $sessionId = '';
            if (isset($params['session_id']) && $params['session_id'] != null) {
                $sessionId = $params['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            try {
                if ($params['type'] == 'grouped') {
                    $params['super_group'] = json_decode($params['super_group'], true);
                } elseif ($params['type'] == 'configurable') {
                    $params['super_attribute'] = json_decode($params['super_attribute'], true);
                    if (array_key_exists('options', $params)) {
                        $params['options'] = json_decode($params['options'], true);
                    }
                } else {
                    if (array_key_exists('options', $params)) {
                        $params['options'] = json_decode($params['options'], true);
                    }
                }

                if ($params['type'] == 'downloadable') {
                    $params['links']= json_decode($params['links'], true);
                }
                if (isset($params['qty'])) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get(
                            \Magento\Framework\Locale\ResolverInterface::class
                        )->getLocale()]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct($productId);
                $related = $this->getRequest()->getParam('related_product');

                if (!$product) {
                    $response['status'] = 'error';
                    $response['message'] = 'Product not found';
                    $jsonResult->setData($response);
                    return $jsonResult;
                }
                $this->_customerCart->getQuote()->setStoreId($storeId);
                $this->_customerCart->addProduct($product, $params);
                if (!empty($related)) {
                    $this->_customerCart->addProductsByIds(explode(',', $related));
                }
                $this->_customerCart->save();

                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->_request, 'response' => $this->getResponse()]
                );

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->_customerCart->getQuote()->getHasError()) {
                        if ($this->shouldRedirectToCart()) {
                            $message = __(
                                'You added %1 to your shopping cart.',
                                $product->getName()
                            );
                        } else {
                            $message = array(
                                'addCartSuccessMessage',
                                [
                                'product_name' => $product->getName()
                                ]
                            );
                        }
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $message = $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage());
                    
                    $jsonResult->setData($message);
                    return $jsonResult;
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $message = $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message);
                        $jsonResult->setData(array("status" => "false", "message" => $message));
                        return $jsonResult;
                    }
                }
                $url = $this->_checkoutSession->getRedirectUrl(true);
                if (!$url) {
                    $url = $this->_redirect->getRedirectUrl($this->getCartUrl());
                }
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $jsonResult->setData($e->getMessage());
                return $jsonResult;
            }
            $totals = $this->_checkoutSession->getQuote()->getTotals();
            $items = $this->_customerCart->getQuote()->getAllVisibleItems();
            $cartData = $this->_cartHelper->getCartData($items, $totals, 'add', '', $storeId);
            return $cartData;
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
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->_productRepository->getById($productId, false, $storeId);
            } catch (\NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
