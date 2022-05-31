<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Homepage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;

class Formkey extends \Magento\Framework\App\Action\Action
{
    protected $request;
    protected $jsonFactory;
    protected $cartHelper;

    public function __construct(
        Context $context,
        Http $request,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        return parent::__construct($context);
    }
    /**
     * This function is used for the logout from the app.
     * @return Bool
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        $formKey = $this->_cartHelper->getFormKey();
        $sucessArray = array('form_key' => $formKey);
        $jsonResult->setData($sucessArray);
        return $jsonResult;
    }
}
