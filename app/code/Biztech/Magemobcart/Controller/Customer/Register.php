<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

class Register extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;
    protected $request;
    protected $customerSession;
    protected $subscriberFactory;
    protected $customerFactory;
    protected $eventManager;
    protected $encryption;
    protected $formKey;

    /**
     * @param Context                                          $context
     * @param JsonFactory                                      $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data                 $cartHelper
     * @param \Magento\Framework\App\Request\Http              $request
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory      $subscriberFactory
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory
     * @param \Magento\Framework\Event\Manager                 $eventManager
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryption
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryption,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_subscriberFactory = $subscriberFactory;
        $this->customerFactory  = $customerFactory;
        $this->_eventManager = $eventManager;
        $this->_encryption = $encryption;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }
    /**
     * This function is used for create customer.
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
            $session = $this->_customerSession;
            $session->setEscapeMessages(true);
            if (array_key_exists('password', $postData) && array_key_exists('cpassword', $postData)) {
                $password = $postData['password'];
                $confirmation = $postData['cpassword'];
                if ($password != "" && $confirmation != "") {
                    if (!$this->checkPasswordConfirmation($password, $confirmation)) {
                        $responseArr['status'] = 'false';
                        $responseArr['message'] = 'Please make sure your passwords match.';
                        $jsonResult->setData($responseArr);
                        return $jsonResult;
                    } else {
                        if (array_key_exists('website_id', $postData)) {
                            $websiteId = $postData['website_id'];
                            if ($websiteId != "") {
                                $customerData = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($postData['email']);
                            
                                $isCustomerEmailExist = $customerData->getEntityId();
                                if ($isCustomerEmailExist != "") {
                                    $responseArr['status'] = 'false';
                                    $responseArr['message'] = 'There is already an account with this email address. If you are sure that it is your email address to get your password and access your account.';
                                    $jsonResult->setData($responseArr);
                                    return $jsonResult;
                                } else {
                                    $customer = $this->customerFactory->create();
                                    $customer->setWebsiteId($websiteId);
                                    $customer->setEmail($postData['email']);
                                    $customer->setFirstname($postData['firstname']);
                                    $customer->setLastname($postData['lastname']);
                                    $customer->setForceConfirmed(true);
                                    $customer->setConfirmation(null);
                                    try {
                                        $hashedPassword = $this->_encryption->getHash($postData['password'], true);
                                        $customer->setPasswordHash($hashedPassword);
                                    } catch (\Exception $e) {
                                        $message = $e->getMessage();
                                        $responseArr['message'] = $message;
                                        $jsonResult->setData($responseArr);
                                        return $jsonResult;
                                    }
                                    $customer->save();
                                    try {
                                        if (array_key_exists('email', $postData)) {
                                            if (isset($postData['email'])) {
                                                $this->_subscriberFactory->create()->subscribe($postData['email']);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $message = $e->getMessage();
                                        $responseArr['status'] = 'true';
                                        $responseArr['message'] = $message;
                                        $jsonResult->setData($message);
                                        return $jsonResult;
                                    }
                                    try {
                                        if ($customer->getId() != "") {
                                            $responseArr['status'] = 'true';
                                            $responseArr['message'] = "Registered Successfully";
                                            $responseArr['session_id'] = $customer->getId() . '_' . base64_encode($postData['email']);
                                            $responseArr['customer_id'] = $customer->getId();

                                            $this->_eventManager->dispatch(
                                                'customer_register_success',
                                                ['account_controller' => $this, 'customer' => $customer]
                                            );

                                            $jsonResult->setData($responseArr);
                                            return $jsonResult;
                                        } else {
                                            $responseArr['status'] = 'false';
                                            $responseArr['message'] = "customer not found";
                                            $jsonResult->setData($responseArr);
                                            return $jsonResult;
                                        }
                                    } catch (InputException $e) {
                                        $message = array();
                                        foreach ($e->getErrors() as $error) {
                                            $message = $this->escaper->escapeHtml($error->getMessage());
                                        }
                                        $jsonResult->setData($message);
                                    } catch (LocalizedException $e) {
                                        $message = $e->getMessage();
                                    } catch (\Exception $e) {
                                        $message = $e->getMessage();
                                    }
                                    $responseArr['status'] = 'false';
                                    $responseArr['message'] = $message;
                                    $jsonResult->setData($responseArr);
                                    return $jsonResult;
                                }
                            } else {
                                $responseArr['status'] = 'false';
                                $responseArr['message'] = 'Website id should not blank';
                                $jsonResult->setData($responseArr);
                                return $jsonResult;
                            }
                        } else {
                            $responseArr['status'] = 'false';
                            $responseArr['message'] = 'Website id is missing';
                            $jsonResult->setData($responseArr);
                            return $jsonResult;
                        }
                    }
                } else {
                    $responseArr['status'] = 'false';
                    $responseArr['message'] = 'Password or confirm password should not blank';
                    $jsonResult->setData($responseArr);
                    return $jsonResult;
                }
            } else {
                $responseArr['status'] = 'false';
                $responseArr['message'] = 'Password or confirm password is missing';
                $jsonResult->setData($responseArr);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    /**
     * This function is used for check password and confirm password
     * @param  text $password
     * @param  text $confirmation
     * @return bool
     */
    public function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            return false;
        }
        return true;
    }
}
