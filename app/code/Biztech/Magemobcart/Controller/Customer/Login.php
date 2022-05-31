<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;

class Login extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $cartModel;
    protected $newsletterModel;
    protected $customerModel;
    protected $accountManagementInterface;
    protected $formKey;

    /**
     * @param Context                                          $context
     * @param JsonFactory                                      $jsonFactory
     * @param \Magento\Framework\App\Request\Http              $request
     * @param \Biztech\Magemobcart\Helper\Data                 $cartHelper
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Customer\Model\Customer                 $customerModel
     * @param \Magento\Checkout\Model\Cart                     $cartModel
     * @param \Magento\Newsletter\Model\Subscriber             $newsletterModel
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Newsletter\Model\Subscriber $newsletterModel,
        \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_cartModel = $cartModel;
        $this->_newsletterModel = $newsletterModel;
        $this->_customerModel = $customerModel;
        $this->_accountManagementInterface = $accountManagementInterface;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for customer login
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
            $postData = $this->_request->getParams();
            $responseArr['status'] = 'false';
            $storeId = 1;
            $sessionId = '';
            if (isset($postData['session_id']) && $postData['session_id'] != null) {
                $sessionId = $postData['session_id'];
                if (!$this->_customerSession->isLoggedIn()) {
                    $customer_id = explode("_", $sessionId);
                    $this->_cartHelper->relogin($customer_id[0]);
                }
            }
            if (!empty($postData['username']) && !empty($postData['password'])) {
                try {
                    $password = $postData['password'];
                    try {
                        $session = $this->_customerSession;
                        $customer = $this->_accountManagementInterface->authenticate($postData['username'], $postData['password']);
                        $this->_customerSession->setCustomerDataAsLoggedIn($customer);
                        $this->_customerSession->regenerateId();
                    } catch (EmailNotConfirmedException $e) {
                        $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                        $message = 'This account is not confirmed.';
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = $message;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } catch (UserLockedException $e) {
                        $message = 'You did not sign in correctly or your account is temporarily disabled.';
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = $message;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } catch (AuthenticationException $e) {
                        $message = 'You did not sign in correctly or your account is temporarily disabled.';
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = $message;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } catch (LocalizedException $e) {
                        $message = $e->getMessage();
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = $message;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } catch (\Exception $e) {
                        $message = 'An unspecified error occurred. Please contact us for assistance.';
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = $message;
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    }
                    
                    if ($this->_customerSession->isLoggedIn()) {
                        $customer = $this->_customerSession->getCustomer();
                        $byi_url_id = '';
                        $responseArr['welcome_text'] = 'Welcome, '. ucfirst($this->_customerSession->getCustomer()->getName());
                        $wishListItemCollection = $this->_cartHelper->getWishlistData($customer->getId(), $storeId, $byi_url_id);
                        $responseArr['status'] = 'true';
                        $responseArr['message'] = 'Successfully logged in.';
                        $responseArr['cart_count'] = $this->_cartModel->getQuote()->getItemsCount();
                        $responseArr['wishlist_count'] = count($wishListItemCollection);
                        $responseArr['customer_id'] = $customer->getId();
                        $responseArr['firstname'] = $customer->getFirstname();
                        $responseArr['lastname'] = $customer->getLastname();
                        $subscriber_data = $this->_newsletterModel->loadByEmail($postData['username']);
                        if ($subscriber_data->getId()) {
                            if ($subscriber_data->getSubscriberStatus() == 1) {
                                $responseArr['is_subscribed'] = $subscriber_data->getSubscriberStatus();
                            } else {
                                $responseArr['is_subscribed'] = "0";
                            }
                        } else {
                            $responseArr['is_subscribed'] = "0";
                        }

                        $session_id = $customer->getId() . '_' . base64_encode($postData['username']);

                        if ($session_id) {
                            $responseArr['session_id'] = $session_id;
                            $data = array('username' => $postData['username'], 'devicetoken' => $postData['device_token'], 'customer_id' => $customer->getId(), 'is_logout' => 0,'password' => $postData['password'],'customer_id' => $customer->getId());
                            $this->_cartHelper->addTokenGeneralLogin($data);
                        }
                        $responseArr['byi_enabled'] = false;
                    }
                } catch (Exception $e) {
                    $responseArr['message'] = $message;
                    $responseArr['status'] = 'false';
                    $session->setUsername($postData['username']);
                }
            } else {
                $responseArr['message'] = 'Login and password are required.';
                $responseArr['status'] = 'false';
            }
            $jsonResult->setData($responseArr);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
