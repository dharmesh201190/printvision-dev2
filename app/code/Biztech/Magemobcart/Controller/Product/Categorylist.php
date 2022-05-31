<?php

/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Magemobcart\Controller\Product;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;

class Categorylist extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $request;
    protected $storeManager;
    protected $categoryModel;
    protected $cartHelper;
    protected $formKey;

    /**
     * @param Context                                    $context
     * @param JsonFactory                                $jsonFactory
     * @param Http                                       $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Category            $categoryModel
     * @param \Biztech\Magemobcart\Helper\Data           $cartHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Category $categoryModel,
        \Biztech\Magemobcart\Helper\Data $cartHelper,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->_request = $request;
        $this->_jsonFactory = $jsonFactory;
        $this->_storeManager = $storeManager;
        $this->_categoryModel = $categoryModel;
        $this->_cartHelper = $cartHelper;
        $this->formKey = $formKey;
        $this->_request->setParam('form_key', $this->formKey->getFormKey());

        parent::__construct($context);
    }

    /**
     * This function is used for get all categories list with tree structure.
     * @return Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $jsonResult = $this->_jsonFactory->create();
        if ($this->_cartHelper->isEnable()) {
            if (!$this->_cartHelper->getHeaders()) {
                $errorResult = array('status' => false, 'message' => $this->_cartHelper->getHeaderMessage());
                $jsonResult->setData($errorResult);
                return $jsonResult;
            }
            $postData = $this->_request->getParams();
            try {
                if (array_key_exists('storeid', $postData)) {
                    $storeId = $postData['storeid'];
                    if ($storeId == "") {
                        $storeId = $this->getDefaultStoreId();
                    }
                } else {
                    $storeId = $this->getDefaultStoreId();
                }
                $rootCatId = $this->getRootCategoryId();
                $categoriesArray = array();
                $categoriesArray = $this->getTreeCategories($rootCatId, true, $storeId);
                $count = 1;
                foreach ($categoriesArray as $key => $value) {
                    if (is_array($value)) {
                        $key1 = explode('_', $key);
                        $categoryCollection = $this->_categoryModel->load($key1[1]);
                        $getIsAnchor = $categoryCollection->getIsAnchor();
                        $categoryname = $categoryCollection->getName();
                        if ($getIsAnchor == 1) {
                            $value[9999] = 'View All ';
                            $categoriesData[$key] = $value;
                        } else {
                            $categoriesData[$key] = $value;
                            continue;
                        }
                    } else {
                        $categoriesData[$count][$key] = $value;
                        $count++;
                        continue;
                    }
                }
            } catch (\Exception $e) {
                $categoriesData = array(
                    'status' => 'false',
                    'message' => $e->getMessage()
                );
            }
            $jsonResult->setData($categoriesData);
            return $jsonResult;
        } else {
            $returnExtensionArray = array('enable' => false);
            $jsonResult->setData($returnExtensionArray);
            return $jsonResult;
        }
    }
    /**
     * This function is uesd for get tree structure of categories
     * @param  int $parentId
     * @param  int $isChild
     * @param  int $storeId
     * @return Array
     */
    protected function getTreeCategories($parentId, $isChild, $storeId)
    {
        $allCats = $this->_categoryModel->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', '1')
            ->addAttributeToFilter('include_in_menu', '1')
            ->addAttributeToFilter('parent_id', array('eq' => $parentId))
            ->addAttributeToSort('position')
            ->setStoreId($storeId);

        $html = array();
        if (!empty($allCats)) {
            foreach ($allCats as $category) {
                $html[$category->getId()] = $category->getName();
                $subcats = $category->getChildren();
                if ($subcats != '') {
                    if (!empty($this->getTreeCategories($category->getId(), true, $storeId))) {
                        $html['subcats_' . $category->getId()] = $this->getTreeCategories($category->getId(), true, $storeId);
                    }
                }
            }
        }

        return $html;
    }
    /**
     * This function is used for get default store id
     * @return int
     */
    public function getDefaultStoreId()
    {
        $defaultStoreId = $this->_storeManager->getStore()->getId();
        return $defaultStoreId;
    }

    /**
     * This function is used for get root category id
     * @return int
     */
    public function getRootCategoryId()
    {
        $rootCatId = $this->_storeManager->getStore()->getRootCategoryId();
        return $rootCatId;
    }
}
