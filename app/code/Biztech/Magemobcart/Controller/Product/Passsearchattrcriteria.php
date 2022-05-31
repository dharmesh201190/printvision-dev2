<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Passsearchattrcriteria extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $cartHelper;
    protected $categoryModel;
    protected $formKey;

    /**
     * @param Context                          $context
     * @param JsonFactory                      $jsonFactory
     * @param Http                             $request
     * @param \Biztech\Magemobcart\Helper\Data $cartHelper
     * @param \Magento\Catalog\Model\Category  $categoryModel
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_cartHelper = $cartHelper;
        $this->_categoryModel = $categoryModel;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * This function is used for get search criteria for product list.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        $postData = $this->_request->getParams();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status'=> false,'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $layerResolver = $objectManager->get(\Magento\Catalog\Model\Layer\Resolver::class);
                $filterableAttributes = $objectManager->get(\Magento\Catalog\Model\Layer\Category\FilterableAttributeList::class);
                $filterList = $objectManager->create(
                    \Magento\Catalog\Model\Layer\FilterList::class,
                    [
                    'filterableAttributes' => $filterableAttributes
                    ]
                );
                $categoryId = $postData['category_id'];
                $categoryLayer = $layerResolver->get()->setCurrentCategory($categoryId);
                $category = $this->_categoryModel->load($categoryId);
                $filterAttributes = $filterList->getFilters($categoryLayer);
                $filterArray      = [];

                $i = 0;
                foreach ($filterAttributes as $filter) {
                    $attributeLabel = (string) $filter->getName();
                    $attributeCode  = (string) $filter->getRequestVar();
                    $items          = $filter->getItems();
                    $filterValues   = [];

                    $j = 0;
                    foreach ($items as $item) {
                        $filterValues['attribute_id'] = (string)  $filter->getName();
                        $filterValues['attribute_code']  = (string) $filter->getRequestVar();
                        $filterValues['options'][$j]['label'] = strip_tags($item->getLabel());
                        $filterValues['options'][$j]['value']   = $item->getValue();
                        $filterValues['options'][$j]['count'] = $item->getCount();
                        $swatchesValues = $category->getSwatches($filter, $item, $j);
                        if (!empty($swatchesValues)) {
                            $filterValues[$j]['swatch_value'] = $swatchesValues['swatch_value'];
                            $filterValues[$j]['swatch_type']  = $swatchesValues['swatch_type'];
                        }
                        $j++;
                    }
                    if ($i != 0) {
                        $filter_attributes[] = $filterValues;
                    }
                    if (!empty($filterValues)) {
                        $filterArray[] = $filterValues;
                    }
                }
                $response = array(
                    'attribute_options' => $filterArray,
                );
            } catch (\Exception $e) {
                $categoriesArray = array(
                'status' => 'false',
                'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($response);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
}
