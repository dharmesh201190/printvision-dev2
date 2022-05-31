<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Passsortingcriteria extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $cartHelper;

    /**
     * @param Context                          $context
     * @param JsonFactory                      $jsonFactory
     * @param \Biztech\Magemobcart\Helper\Data $cartHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Biztech\Magemobcart\Helper\Data $cartHelper
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        parent::__construct($context);
    }

    /**
     * This function is used for get sorting crietarea
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
            try {
                $responseArray = array('sort_by' => array('price', 'name'),
                'direction' => array('price' => array('desc', 'asc'), 'name' => array('desc', 'asc')));
            } catch (\Exception $e) {
                $categoriesArray = array(
                'status' => 'false',
                'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($responseArray);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
