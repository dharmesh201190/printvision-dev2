<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Checkout;

use Magento\Checkout\Helper\Data;
use Magento\Checkout\Helper\ExpressRedirect;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception;
use Magento\Paypal\Model\Express\Checkout;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class GetToken
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customerlogin extends \Magento\Framework\App\Action\Action
{
    protected $request;

    /**
     * @param Context                              $context
     * @param \Magento\Checkout\Model\Type\Onepage $onepageCheckout
     * @param \Magento\Framework\App\Request\Http  $request
     * @param JsonFactory                          $jsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        \Magento\Framework\App\Request\Http $request,
        JsonFactory $jsonFactory
    ) {
        $this->_onepageCheckout = $onepageCheckout;
        $this->_jsonFactory = $jsonFactory;
        $this->_request = $request;
        parent::__construct($context);
    }
    /**
     * Force login for android user.
     * @return [type] [description]
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        $postData = $this->_request->getParams();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($this->_request->getParam('customer_id'));
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customerSession->setCustomerAsLoggedIn($customer);
        $redirect = $objectManager->create('Magento\Framework\Controller\Result\Redirect');
        $redirectLink = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
        if (!$customerSession->isLoggedIn()) {
            $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($this->_request->getParam('customer_id'));
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $customerSession->setCustomerAsLoggedIn($customer);
            $redirect->setUrl($redirectLink);
            return $redirect;
        } else {
            $redirect->setUrl($redirectLink);
            return $redirect;
        }
    }
    public function getOnepage()
    {
        return $this->_onepageCheckout;
    }
}
