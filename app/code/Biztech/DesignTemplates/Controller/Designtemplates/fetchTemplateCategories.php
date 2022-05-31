<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\DesignTemplates\Controller\Designtemplates;

class fetchTemplateCategories extends \Biztech\DesignTemplates\Controller\Designtemplates {

    protected $allstore = [];
    protected $templateCategories = [];
    protected $productDesignTemplatesCategory = [];
    protected $isTemplate = [];

    public function execute() {
        try {
            /*
             * Fetch Params
             */
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $productId = $data['productId'];
            $this->isTemplate = isset($data['isTemplate']) ? $data['isTemplate'] : '';
            $cacheKey = 'fetchTemplateCategories' . $productId . $this->isTemplate;

            $response = $this->templateHelper->loadCache($cacheKey);
            if (!$response) {
                /**
                 * If this request is from templates tab then fetch only products categories data
                 */
                $product = $this->_productLoader->create()->load($productId);
                if ($this->isTemplate === true) {
                    if(!$product->getDesignerProductType()){
                        if($product->getDesignTemplatesCategory()){
                            $this->productDesignTemplatesCategory = explode(",", $product->getDesignTemplatesCategory());
                        }
                    }
                }
                $templateCategories = !$product->getDesignerProductType() ? $this->fetchTemplateCategories() : array();
                $defaultTemplateCategory = count($templateCategories) > 0 ? $templateCategories[0] : null;
                /*
                 * Fetch all designs of first template category if this request is from templates
                 */
                if ($this->isTemplate === true) {
                    if(!$product->getDesignerProductType()){
                        $templateData = array(
                            'templateCatId' => $defaultTemplateCategory['id'],
                        );
                        $response = $this->templateHelper->fetchTemplates($templateData);
                    }else{
                        // fetch calender product templates data

                        $templateData = array(
                            'productId' => $productId,
                        );
                        $calenderObject = $this->_objectFactory->create();
                        $calenderObject->setTemplateData($templateData);
                        $this->_eventManager->dispatch('fetchCalenderProductTemplate', ['calenderObject' => $calenderObject]);
                        $response = $calenderObject->getTemplateData();
                    }
                }
                $response['templateCategories'] = $templateCategories;
                $response['defaultTemplateCategory'] = $defaultTemplateCategory;
                $response['status'] = 'success';
                $this->templateHelper->setCache($response, $cacheKey);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->templateHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function fetchTemplateCategories() {
        $storeid = $this->_storeManager->getStore()->getId();
        array_push($this->allstore, 0);
        array_push($this->allstore, $storeid);
        $templateCategoriesCollection = $this->templateCategoryFactory->create()
                ->addFieldToFilter("is_root_category", array("eq" => 1))
                ->addFieldToFilter("status", array("eq" => 1))
                ->addFieldToFilter("store_id", array("in" => $this->allstore));
        $templateCategoriesCollection->getSelect()->order('category_title ASC');
        $this->templateCategories = [];
        foreach ($templateCategoriesCollection as $templateCat) {
            $allowToAdd = true;
            if ($this->isTemplate === true && !in_array($templateCat->getId(), $this->productDesignTemplatesCategory)) {
                $allowToAdd = false;
            }
            if ($allowToAdd) {
                $this->templateCategories[] = array(
                    'id' => $templateCat->getId(),
                    'title' => $templateCat->getCategoryTitle(),
                    'level' => 0
                );
            }
            $this->getSubCategories($templateCat->getId());
        }
        return $this->templateCategories;
    }

    public function getSubCategories($parentTemplateCatId) {
        $templateCategoriesCollection = $this->templateCategoryFactory->create()
                ->addFieldToFilter('parent_categories', array('eq' => $parentTemplateCatId))
                ->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter("store_id", array("in" => $this->allstore));
        if ($templateCategoriesCollection->count() > 0) {
            foreach ($templateCategoriesCollection as $templateCat) {
                $allowToAdd = true;
                if ($this->isTemplate === true && !in_array($templateCat->getId(), $this->productDesignTemplatesCategory)) {
                    $allowToAdd = false;
                }
                if ($allowToAdd) {
                    $this->templateCategories[] = array(
                        'id' => $templateCat->getId(),
                        'title' => $templateCat->getCategoryTitle(),
                        'level' => $templateCat->getLevel(),
                    );
                }
                $this->getSubCategories($templateCat->getId());
            }
        }
    }

}
