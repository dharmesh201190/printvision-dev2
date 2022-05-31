<?php

namespace Biztech\Productdesigner\Controller\Catalog;

class getCategory extends \Biztech\Productdesigner\Controller\Catalog {

    const Identifier = 'getCategories';

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = !empty($data['productId']) ? $data['productId'] : "";
            $search = !empty($data['search']) ? $data['search'] : "";
            $limit = !empty($data['limit']) ? $data['limit'] : "";
            $categoryId = !empty($data['categoryId']) ? $data['categoryId'] : "";
            $cacheKey = self::Identifier . $productId . $categoryId . $search . $limit;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $store = $this->_storeManager->getStore($storeid);
                if (isset($data['action']) && $data['action'] == 'onChange') {
                    $categoryId = $data['categoryId'];
                } else {
                    $product = $this->_productLoader->create()->load($productId);
                    $ids = $product->getCategoryIds();
                    $categoryId = (isset($ids[1])) ? $ids[1] : [];
                    foreach ($ids as $key => $value) {
                        if ($value == '2') {
                            continue;
                        } else if (isset($ids[$key])) {
                            $categoryId = $ids[$key];
                            break;
                        } else if (!isset($ids[$key])) {
                            $categoryId = null;
                        }
                    }
                    $enableAllProducts = $this->_pdHelper->getConfig('productdesigner/enable_allproducts/enable', $storeid);
                    if ($enableAllProducts == '1') {
                        $response['catalogCategory'] = $this->fetchCatalogParentCategories($store);
                        $response['catalogProducts'] = $this->_infoHelper->fetchCatalogProducts($categoryId, $store, $search, $limit);
                        $response['defaultCatId'] = $categoryId;
                    }
                    $response['enableAllProducts'] = $enableAllProducts;
                    $this->_infoHelper->setCache($response, $cacheKey);
                }
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function fetchCatalogParentCategories($store) {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
            $productcategories = [];
            $categories = $categoryCollection->create()
                    ->setStore($store)
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('is_active', array('eq' => '1'))
                    ->addFieldToFilter('level', array('eq' => '2'))
                    ->setOrder('name', 'ASC');
            foreach ($categories as $category) {
                $productcategories[] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'level' => $category->getLevel(),
                    'path' => $category->getPath(),
                    'parentId' => null,
                );
                $this->getSubCategories($category, $categoryCollection, $productcategories, $store);
            }
            return $productcategories;
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function getSubCategories($category, $categoryCollection, &$productcategories, $store) {
        $subCategories = $categoryCollection->create()
                ->setStore($store)
                ->addAttributeToSelect('*')
                ->addFieldToFilter('is_active', array('eq' => '1'))
                ->addIdFilter($category->getChildren())
                ->setOrder('name', 'ASC');
        foreach ($subCategories as $subCategory) {
            $productcategories[] = array(
                'id' => $subCategory->getId(),
                'name' => $subCategory->getName(),
                'level' => $subCategory->getLevel(),
                'parentId' => $category->getId(),
                'path' => $category->getPath(),
            );
            if ($subCategory->getLevel() < '5') {
                $this->getSubCategories($subCategory, $categoryCollection, $productcategories, $store);
            }
        }
    }

}
