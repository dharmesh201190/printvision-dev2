<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Getcontactusdetail extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'contact/email/email_template';
    const XML_PATH_ENABLED = 'contact/contacts/enabled';

    protected $jsonFactory;
    protected $cartHelper;
    protected $inlineTranslation;
    protected $request;
    protected $mail;
    protected $formKey;
    
    /**
     * @param Context                                            $context
     * @param Http                                               $request
     * @param JsonFactory                                        $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data                   $cartHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param MailInterface                                      $mail
     */
    public function __construct(
        Context $context,
        Http $request,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        MailInterface $mail,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->inlineTranslation = $inlineTranslation;
        $this->_request = $request;
        $this->mail = $mail;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get the details submit by customer for inquiry
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
            try {
                if (!$this->getRequest()->isPost()) {
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
                try {
                    $this->sendEmail($this->validatedParams());
                    $returnExtensionArray = array('status' => 'true', 'message' => 'Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.');
                    $jsonResult->setData($returnExtensionArray);
                    return $jsonResult;
                } catch (LocalizedException $e) {
                    $jsonResult->setData($e->getMessage());
                    return $jsonResult;
                } catch (\Exception $e) {
                    $returnExtensionArray = array('An error occurred while processing your form. Please try again later.');
                    $jsonResult->setData($returnExtensionArray);
                    return $jsonResult;
                }
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $returnExtensionArray = array('status' => false,'message' => 'We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage());
                $jsonResult->setData($returnExtensionArray);
                return $jsonResult;
            }
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }

    private function sendEmail($post)
    {
        $this->mail->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }

    private function validatedParams()
    {
        $request = $this->_request;
        if (trim($request->getParam('name')) === '') {
            $returnExtensionArray = array('status' => 'false', 'message' => 'Name is missing');
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
        if (trim($request->getParam('comment')) === '') {
            $returnExtensionArray = array('status' => 'false', 'message' => 'Comment is missing');
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
        if (false === \strpos($request->getParam('email'), '@')) {
            $returnExtensionArray = array('status' => 'false', 'message' => 'Invalid email address');
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
        return $request->getParams();
    }
}
