<?php

namespace Biztech\DesignTemplates\Helper;

class Template extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_infoHelper;
    protected $designTemplates;
    protected $designTemplateCategory;
    protected $designTemplatesCollection;
    protected $_productLoader;
    protected $_storeManager;
    protected $_designtemplateCategory;
    protected $_productCollectionFactory;
    protected $_eventManager;

    public function __construct(
        \Magento\Framework\Event\Manager $manager,
        \Biztech\Productdesigner\Helper\Info $infoHelper,
        \Biztech\DesignTemplates\Model\DesigntemplatesFactory $designtemplates,
        \Biztech\DesignTemplates\Model\Mysql4\Designtemplates\CollectionFactory $designTemplatesCollection,
        \Magento\Catalog\Model\ProductFactory $_productLoader,
        \Biztech\DesignTemplates\Model\DesigntemplatecategoryFactory $designTemplateCategory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Biztech\DesignTemplates\Model\Mysql4\Designtemplatecategory\Collection $designtemplateCategory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->designTemplates = $designtemplates;
        $this->designTemplateCategory = $designTemplateCategory;
        $this->designTemplatesCollection = $designTemplatesCollection;
        $this->_infoHelper = $infoHelper;
        $this->_productLoader = $_productLoader;
        $this->_storeManager = $storeManager;
        $this->_designtemplateCategory = $designtemplateCategory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_eventManager = $manager;
    }

    public function throwException($e, $class) {
        return $this->_infoHelper->throwException($e, $class);
    }

    public function loadCache($cacheKey) {
        return $this->_infoHelper->loadCache($cacheKey);
    }

    public function setCache($response, $cacheKey) {
        $this->_infoHelper->setCache($response, $cacheKey);
    }

    public function saveProductTemplate($data) {
        $title = isset($data['title']) ? $data['title'] : '';
        $status = isset($data['status']) ? $data['status'] : 1; 
        $editedTemplateId = isset($data['templateId']) ? base64_decode($data['templateId']) : '';
        $isTemplateExist = $this->isTemplateExist($title, $editedTemplateId);
        $response['status'] = 'success';
        if ($isTemplateExist) {
            $response = ['message' => __('Template already exists')];
            $response['status'] = 'error';
            $response['isExists'] = true;
            return $response;
        }
        $dataUrls = ($data['dataUrl']) ?: '';
        $product_image_data = ($data['product_image_data']) ?: '';
        $product_id = ($data['product_id']) ?: '';
        $canvasJson = ($data['canvasJson']) ?: '';
        $associated_product_id = isset($data['associated_product_id']) ? $data['associated_product_id'] : $product_id;
        $isPreLoaded = isset($data['isPreLoaded']) ? $data['isPreLoaded'] : false;
        $selectedTemplateCategories = isset($data['selectedTemplateCategories']) ? $data['selectedTemplateCategories'] : [];
        /*
         * Convert relative path to absolute path
         */
        $product_image_path = $this->_infoHelper->convertRelToAbsPath($product_image_data['url']);
        $product_image_data['path'] = $product_image_path;
        /**
         * Save canvas objects data into design table
         */

        // check template status
        $templateStatus = '';
        if($editedTemplateId){
            $template = $this->designTemplates->create()->load($editedTemplateId);
            $templateCurrentStatus = $template->getStatus();
            $templateStatus = $templateCurrentStatus != $status && $status == 0 ? 'disabled' : '';
        }

        $params = array(
            'template_title' => $title,
            'product_id' => $product_id,
            'associated_product_id' => $associated_product_id,
            'json_objects' => base64_decode($canvasJson),
            'status' => $status
        );
        $templateId = $this->saveTemplate($params, $editedTemplateId);
        
        /**
         * Generate image in designs folder
         */
        $type = array('path' => 'templates','id' => $templateId);
        $response['images'] = $this->_infoHelper->generateImage($dataUrls, $product_image_data, $type);
        $response['templateId'] = $templateId;
        
        $message = $templateStatus == 'disabled' ? __("Template disabled successfully") : __("Template saved successfully");
        /**
         * Save generated image paths in table
         */
        $this->saveGenerateImages($response);

        /**
         * Assign pre loaded template and template catergories
         */
        $this->setTemplateDataToProduct($product_id, $associated_product_id, $templateId, $isPreLoaded, $selectedTemplateCategories);

        $response['message'] = $message;
        return $response;
    }

    private function isTemplateExist($title, $templateId) {
        $designTemplatesData = $this->designTemplatesCollection->create()
        ->addFieldToFilter('designtemplates_id', array('neq' => $templateId))
        ->addFieldToFilter('template_title', $title)->getFirstItem()->getData();
        if (count($designTemplatesData) > 0) {
            return true;
        }
        return false;
    }

    protected function setTemplateDataToProduct($product_id, $associated_product_id, $templateId, $isPreLoaded, $selectedTemplateCategories) {
        $product = $this->_productLoader->create()->load($product_id);

        /**
         * Set PreLoadedTemplate
         */
        if ($isPreLoaded) {
            $product->setPreLoadedTemplate($templateId);
            if ($product->getTypeId() == 'configurable') {
                $associatedProduct = $this->_productLoader->create()->load($associated_product_id);
                $associatedProduct->setPreLoadedTemplate($templateId);
                $associatedProduct->save();
            }
        }else{
            $product->setPreLoadedTemplate('');
            if ($product->getTypeId() == 'configurable') {
                $associatedProduct = $this->_productLoader->create()->load($associated_product_id);
                $associatedProduct->setPreLoadedTemplate('');
                $associatedProduct->save();
            }
        }
        /**
         * Fetch existing design template category and merge with new selected categories and unique id are updated
         */
        $existingTemplateCategories = explode(",", $product->getDesignTemplatesCategory());
        $updatedTemplateCategories = implode(",", array_unique(array_merge($existingTemplateCategories, $selectedTemplateCategories)));
        $product->setDesignTemplatesCategory($updatedTemplateCategories);
        $product->save();
        $this->_eventManager->dispatch('save_preloaded_design_template', ['product' => $product]);
        /**
         * Set design template to selected categories
         */
        // foreach ($this->_designtemplateCategory as $templateCat) {
        //     if ($templateCat != '' || $templateCat != null) {
        //         $templates = explode(",", $templateCat->getDesigns());
        //         $index = array_search($templateId, $templates) ;
        //         if ($index != -1) {
        //             unset($templates[$index]);
        //         }
        //         $templateCat->setDesigns(implode(",", $templates));
        //         $templateCat->save();
        //     }
        // }

        foreach ($selectedTemplateCategories as $selectedTemplateCategory) {
            $templateCategory = $this->designTemplateCategory->create()->load($selectedTemplateCategory);
            $existingTemplates = explode(",", $templateCategory->getDesigns());
            $existingTemplates[] = $templateId;
            $updatedTemplates = implode(",", array_unique($existingTemplates));
            $templateCategory->setDesigns($updatedTemplates);
            $templateCategory->save();
        }
    }

    private function saveTemplate($params, $editedTemplateId) {
        if ($editedTemplateId) {
            $template = $this->designTemplates->create()->load($editedTemplateId);
        } else {
            $template = $this->designTemplates->create();
        }
        $template->setData($params);
        if ($editedTemplateId) {
            $template->setDesigntemplatesId($editedTemplateId);
        }
        $template->save();
        return $template->getDesigntemplatesId();
    }

    private function saveGenerateImages($params) {
        $imageName = $params['images']['base']['name'];
        $template = $this->designTemplates->create()->load($params['templateId']);
        $template->setImage($imageName);
        $template->save();
    }

    public function loadTemplate($templateId) {
        $designTemplates = $this->designTemplates->create()->load($templateId)->getData();
        $productId= $designTemplates['product_id'];
        $unsetArray = array(
            'created_at',
            'updated_at',
        );
        foreach ($unsetArray as $unsetVal) {
            unset($designTemplates[$unsetVal]);
        }
        /*
         * Fetch Category of Template
         */
        $selectedTemplateCategories = array();
        foreach ($this->designTemplateCategory->create()->getCollection() as $templateCat) {
            $templates = explode(",", $templateCat->getDesigns());
            if (in_array($templateId, $templates)) {
                $selectedTemplateCategories[] = $templateCat->getId();
            }
        }
        $designTemplates['selectedTemplateCategories'] = $selectedTemplateCategories;
        $productData = $this->_productLoader->create();
        $preLoadedTemplateId= $productData->load($productId)->getPreLoadedTemplate();
        if($preLoadedTemplateId == $templateId){
            $designTemplates['isPreLoaded']='true';
        }
        else{
            $designTemplates['isPreLoaded']='false';
        }

        $designTemplates['statusTemplate'] = $designTemplates['status'];
        return $designTemplates;
    }

    public function fetchTemplates($data) {
        $templateData = [];
        $id = $data['templateCatId'];
        $productDesignTemplatesCategories = isset($data['productDesignTemplatesCategory']) ? $data['productDesignTemplatesCategory'] : [];
        $searchText = isset($data['searchText']) ? $data['searchText'] : '';
        $page = isset($data['page']) ? $data['page'] : 1;
        $limit = isset($data['limit']) ? $data['limit'] : 12;
        $enabledProducts = [];
        $products = $this->_productCollectionFactory->create();
        $products->addAttributeToSelect('*');
        $products->addAttributeToFilter('status', array("eq" => 1));
        $products->addAttributeToFilter('type_id', array("in" => array('simple', 'configurable')));
        $products->addAttributeToFilter('enable_product_designer', array("eq" => 1));
        foreach ($products as $key => $value) {
            // echo "<pre>";
            // print_r($value->getData());
            array_push($enabledProducts, $value->getData()['entity_id']);
        }
        // echo "<pre>";
        // print_r($enabledProducts);
        // exit();
        

        /**
         * Fetch design templates filtered with template title
         */
        $designTemplatesCollection = $this->designTemplatesCollection->create()
        ->addFieldToSelect(array("designtemplates_id", "image", "json_objects", "template_title", "product_id"))
        ->addFieldToFilter(
            array('template_title', 'template_title'), array(
                array('like' => '%' . $searchText . '%'),
                array('null' => true)
            )
        );
        /**
         * If search text not found than fetch templates of current cat only else fetch from all cat
         */
        /*if ($searchText == '') {*/
            $designTemplates = explode(",", $this->designTemplateCategory->create()->load($id)->getDesigns());
            $designTemplatesCollection->addFieldToFilter("designtemplates_id", array("in" => $designTemplates));
            $designTemplatesCollection->addFieldToFilter("product_id", array("in" => $enabledProducts));
        /*}*/ /*else if (count($productDesignTemplatesCategories) != 0) {
            $designTemplates = '';
            foreach ($productDesignTemplatesCategories as $productDesignTemplatesCategory) {
                $designTemplates .= "," . $this->designTemplateCategory->create()->load($productDesignTemplatesCategory)->getDesigns();
            }
            $designTemplatesCollection->addFieldToFilter("designtemplates_id", array("in" => explode(",", $designTemplates)));
            $designTemplatesCollection->addFieldToFilter("product_id", array("in" => $enabledProducts));
        }*/
        $designTemplatesCollection->addFieldToFilter("status", array("eq" => 1));
        $totalRecords = count($designTemplatesCollection->getData());
        $response['templates'] = $designTemplatesCollection->setPageSize($limit)
        ->setCurPage($page)
        ->load()
        ->getData();

        $response['templateImgUrl'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/templates/';
        if ($limit <= $totalRecords) {
            $loadMoreFlag = 1;
        } else {
            $loadMoreFlag = 0;
        }
        $totalpages = ceil($totalRecords / $limit);
        if ($page == $totalpages) {
            $loadMoreFlag = 0;
        }
        $response['loadMoreFlag'] = $loadMoreFlag;
        return $response;
    }

    public function getSuperAttributesFromTemplate($templateId) {
        $template = $this->designTemplates->create()->load($templateId);
        $product = $this->_productLoader->create()->load($template->getProductId());
        if ($product->getTypeId() == 'configurable') {
            $associatedProduct = $this->_productLoader->create()->load($template->getAssociatedProductId());
            $configurableOptions = $this->_infoHelper->fetchConfigurableOptions($product, $associatedProduct);
            return $configurableOptions[0];
        }
        return null;
    }

}
