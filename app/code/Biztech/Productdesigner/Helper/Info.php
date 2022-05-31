<?php

namespace Biztech\Productdesigner\Helper;
use Magento\Framework\Serialize\Serializer\Serialize;

class Info extends \Magento\Framework\App\Helper\AbstractHelper {

    const ResizeWidth = 540;
    const ResizeHeight = 540;
    const CanvasRatio = 40;
    const XML_PATH_ENABLED = 'productdesigner/activation/enable';
    const XML_PATH_INSTALLED = 'productdesigner/activation/installed';
    const XML_PATH_KEY = 'productdesigner/activation/key';
    const XML_PATH_EN = 'productdesigner/activation/en';
    const XML_PATH_DATA = 'productdesigner/activation/data';
    const XML_PATH_WEBSITES = 'productdesigner/activation/websites';

    protected $configurable;
    protected $_helper;
    protected $_pdHelper;
    protected $_storeManager;
    protected $_productLoader;
    protected $cache;
    protected $directoryList;
    protected $mediaPath;
    protected $mediaURL;
    protected $directoryHelper;
    protected $localeFormat;
    protected $helper;
    protected $design;
    protected $designImages;
    protected $_catalogModel;
    protected $_imagehelper;
    protected $_stockFilter;
    protected $_productCollectionFactory;
    protected $scopeConfig;
    protected $_encryptor;
    protected $_filesystem;
    protected $_eventManager;
    protected $_cacheTimeLimit;
    protected $selectionAreaCollection;
    protected $maskingFactory;
    protected $sideFactory;
    protected $designFactory;
    protected $stockRegistry;
    protected $swatchCollection;
    protected $swatchFactory;
    protected $calculation;
    protected $priceHelper;
    protected $currencyFactory;
    protected $imageSides;
    protected $_productModel;
    protected $_serialize;

    protected $_objectFactory;

    public function __construct(
        \Magento\Catalog\Helper\Image $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Biztech\Productdesigner\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductFactory $_productLoader,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Tax\Helper\Data $taxhelper,
        \Biztech\Productdesigner\Model\Designs $design,
        \Magento\Catalog\Model\Category $catalogModel,
        \Magento\Catalog\Helper\Image $imagehelper,
        \Magento\CatalogInventory\Helper\Stock $_stockFilter,
        \Biztech\Productdesigner\Model\DesignimagesFactory $designimagesFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Framework\Event\Manager $manager,
        \Biztech\Productdesigner\Model\Mysql4\Selectionarea\CollectionFactory $selectionAreaCollection,
        \Biztech\Productdesigner\Model\MaskingFactory $maskingFactory,
        \Biztech\Productdesigner\Model\SideFactory $sideFactory,
        \Biztech\Productdesigner\Model\DesignsFactory $designFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollection,
        \Magento\Swatches\Model\SwatchFactory $swatchFactory,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\Product $productModel,
        Serialize $serializer,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_encryptor = $encryptor;
        $this->_productLoader = $_productLoader;
        $this->_helper = $helper;
        $this->_pdHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->cache = $cache;
        $this->mediaPath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . DIRECTORY_SEPARATOR;
        $this->mediaURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->directoryHelper = $directoryHelper;
        $this->localeFormat = $localeFormat;
        $this->helper = $taxhelper;
        $this->design = $design;
        $this->designImagesFactory = $designimagesFactory;
        $this->_catalogModel = $catalogModel;
        $this->_imagehelper = $imagehelper;
        $this->_stockFilter = $_stockFilter;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_filesystem = $filesystem;
        $this->configurable = $configurable;
        $this->_eventManager = $manager;
        $storeid = $this->_storeManager->getStore()->getId();
        $this->_cacheTimeLimit = $this->_pdHelper->getConfig('productdesigner/general/cache_life_time', $storeid);
        $this->selectionAreaCollection = $selectionAreaCollection;
        $this->maskingFactory = $maskingFactory;
        $this->sideFactory = $sideFactory;
        $this->designFactory = $designFactory;
        $this->stockRegistry = $stockRegistry;
        $this->swatchCollection = $swatchCollection;
        $this->swatchFactory = $swatchFactory;
        $this->calculation = $calculation;
        $this->priceHelper = $priceHelper;
        $this->currencyFactory = $currencyFactory;
        $this->_productModel = $productModel;
        $this->_serialize = $serializer;
        $this->_objectFactory = $objectFactory;
    }

    /**
     * Fetch simple product's dimension
     * @param type $product
     * @param type $configurableProductId
     * @return array
     */
    public function getSimpleProductDimensionsData($product, $configurableProductId) {
        $designer_images = array();
        $productId = $product->getId();
        $parentProductId = (!$configurableProductId) ? $productId : $configurableProductId;
        $parentProduct = $this->_productLoader->create()->load($parentProductId);
        $product_images = $product->getAllMediaGalleryImages();
        if ($product_images) {
            $isEnableHandles = $this->IsEnableHandles($parentProductId);
            $designer_images = array();
            foreach ($product_images as $product_image) {
                $dimensions = $this->selectionAreaCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('image_id', $product_image->getId())
                ->getData();
                $isParent = false;
                /**
                 * If dimension not found search for its parents dimension
                 */
                $parentImageId = '';
                if (count($dimensions) <= 0) {
                    $dimensions = $this->selectionAreaCollection->create()
                    ->addFieldToFilter('product_id', $parentProductId)
                    ->addFieldToFilter('imageside_id', $product_image->getImageSideDefault())
                    ->getData();
                    $isParent = true;
                } else {
                    $parentDimension = $this->selectionAreaCollection->create()
                    ->addFieldToFilter('product_id', $parentProductId)
                    ->addFieldToFilter('imageside_id', $product_image->getImageSideDefault())
                    ->getData();
                    if (count($parentDimension) > 0) {
                        $parent_selection_area = json_decode($parentDimension[0]['selection_area'], true);
                        $parentImageId = $parent_selection_area['image_id'];
                    }
                }
                if (count($dimensions) > 0 && $product_image->getImageSideDefault()) {
                    list($iWidth, $iHeight) = getimagesize($product_image->getPath());
                    /*
                     * It will calculate resize height and width based on product image
                     */
                    list($resize_width, $resize_height) = $this->calculateResizeWidthHeight($product_image->getPath());
                    /**
                     *  Resize image based on resize height and width to display on frontend
                     */
                    $image_url = $this->_helper->init($product, 'product_page_image')
                    ->setImageFile($product_image->getFile())
                    ->resize($resize_width, $resize_height)
                    ->keepAspectRatio(true)->constrainOnly(false)->getUrl();
                    /**
                     * Fetch all dimesions into single array
                     */
                    $selection_areas = array();
                    for ($i = 0; $i < count($dimensions); $i++) {
                        $selection_area = json_decode($dimensions[$i]['selection_area'], true);
                        $selection_area['designareaId'] = $dimensions[$i]['design_area_id'];
                        $maskURL = null;
                        if (!empty($dimensions[$i]['masking_image_id']) && $dimensions[$i]['masking_image_id']) {
                            $maskURL = $this->getMaskingUrl($dimensions[$i]['masking_image_id']);
                        }
                        $selection_area['mask'] = $maskURL;
                        if ($isParent) {
                            $selection_area['parent_image_id'] = $selection_area['image_id'];
                            $selection_area['image_id'] = $product_image->getId();
                        } else {
                            $selection_area['parent_image_id'] = ($parentImageId) ?: $selection_area['image_id'];
                        }
                        $selection_areas[] = $selection_area;
                    }
                    $imageside_title = $this->imageSides[$product_image->getImageSideDefault()];
                    /**
                     * Prepare data of images and its dimension to pass on tool page
                     */
                    $customOutputWidth = $product->getOutputWidth() ?: $parentProduct->getOutputWidth();
                    $customOutputHeight = $product->getOutputHeight() ?: $parentProduct->getOutputHeight();

                    $designerItem = array(
                        'url' => $image_url,
                        'image_id' => $product_image->getId(),
                        'image_title' => $imageside_title,
                        'parent_image_id' => isset($selection_areas[0]) ? $selection_areas[0]['parent_image_id'] : $product_image->getId(),
                        'dim' => $selection_areas,
                        'width' => $resize_width,
                        'height' => $resize_height,
                        'size' => array(
                            'origWidth' => $customOutputWidth ?: $iWidth,
                            'origHeight' => $customOutputHeight ?: $iHeight,
                            'toolWidth' => $resize_width,
                            'toolHeight' => $resize_height
                        ),
                        'image_side' => $product_image->getImageSideDefault(),
                        'enable_handles' => (count($dimensions) > 1) ? false : $isEnableHandles
                    );

                    $customObject = $this->_objectFactory->create();
                    $customObject->setDesignerItem($designerItem);
                    $this->_eventManager->dispatch('designer_images_response_before', ['designerItem' => $customObject]);
                    $designerItem = $customObject->getDesignerItem();
                    $designer_images[] = $designerItem;
                }
            }
        }
        return $designer_images;
    }

    /**
     * Main get image dimension to check product type and get dimension
     * @param type $productId main product id
     * @param type $selectedOptions options which are selected on front
     * @return return
     */
    public function getImageDimension($productId, $selectedOptions = '', $designId = '') {

        $storeid = $this->_storeManager->getStore()->getId();
        $product = $this->_productLoader->create()->load($productId);

        $product_type = $product->getTypeId();

        $response = array();
        $selectedOptionsValues = array();
        $this->imageSides = $this->getAllImageSides();
        if ($product_type == 'configurable' && !$product->getDesignerProductType()) {
            /**
             * If Options are there then fetch its associated product
             */
            if ($selectedOptions) {
                $defaultAssociatedProductId = $this->fetchSelectedAssociatedProduct($selectedOptions, $product);
            } else if ($designId) {
                $design = $this->designFactory->create()->load($designId);
                $defaultAssociatedProductId = $design->getAssociatedProductId();
            } else {
                $defaultAssociatedProductId = $product->getDefaultAssociatedProduct();
            }
            $childProducts = $this->configurable->getChildrenIds($productId, $product);

            foreach ($childProducts[0] as $childProductid) {
                if ($childProductid == $defaultAssociatedProductId) {
                    /**
                     * Fetch default AssociatedProduct product's Options
                     */
                    $defaultAssociatedProduct = $this->_productLoader->create()->load($defaultAssociatedProductId);
                    list($selectedOptions, $selectedOptionsValues) = $this->fetchConfigurableOptions($product, $defaultAssociatedProduct);
                    /**
                     * Fetch dimensions of product
                     */
                    $designer_images = $this->getSimpleProductDimensionsData($defaultAssociatedProduct, $productId);
                }
            }
            $defaultProductId = $defaultAssociatedProductId;
        } else if ($product_type == 'simple' || $product->getDesignerProductType()) {
            $designer_images = $this->getSimpleProductDimensionsData($product, '');
            $defaultProductId = $productId;
        }

        $isImageThumnailEnable = $this->_pdHelper->getConfig('productdesigner/general/enable_image_thumbnails', $storeid);
        $productThumnailEnable = $product->getEnableImageThumbnails();
        if ($productThumnailEnable == '0' || $productThumnailEnable == '1') {
            $isImageThumnailEnable = (int)$productThumnailEnable;
        } 
        $response['dimensions'] = $designer_images;
        $response['selectedOptions'] = $selectedOptions;
        $response['selectedOptionsValues'] = $selectedOptionsValues;
        $response['productType'] = $product_type;
        $response['associatedProductId'] = $defaultProductId;
        $response['designerProductType'] = $product->getDesignerProductType();
        $response['isImageThumnailEnable'] = (int)$isImageThumnailEnable;
        return $response;
    }

    public function getMaskingUrl($masking_image_id) {
        $maskingModel = $this->maskingFactory->create()
        ->load($masking_image_id);
        if ($maskingModel['masking_path']) {
            return $this->mediaURL . 'productdesigner/masking' . $maskingModel['masking_path'];
        }
        return null;
    }

    public function getMaskingPath($masking_image_id) {
        $maskingModel = $this->maskingFactory->create()
        ->load($masking_image_id);
        if ($maskingModel['masking_path']) {
            return $this->mediaPath . '/productdesigner/masking' . $maskingModel['masking_path'];
        }
        return null;
    }

    /**
     * fetch super attributes/configurable options of associated product
     * @param type $product
     * @param type $defaultAssociatedProduct
     * @return array
     */
    public function fetchConfigurableOptions($product, $defaultAssociatedProduct) {
        $attrs = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
        $selectedOptions = array();
        foreach ($attrs as $attr) {
            $selectedOptions[$attr['attribute_id']] = $defaultAssociatedProduct->getData($attr['attribute_code']);
            $selectedOptionsValues[] = $defaultAssociatedProduct->getData($attr['attribute_code']);
        }
        return array($selectedOptions, $selectedOptionsValues);
    }

    /**
     * Fetch associated product based on selected options
     * @param type $selectedOptions
     * @param type $product
     * @return int
     */
    public function fetchSelectedAssociatedProduct($selectedOptions, $product) {
        $childProduct = $this->configurable->getProductByAttributes($selectedOptions, $product);
        if ($childProduct) {
            return $childProduct->getId();
        }
        return null;
    }

    /**
     * function to check for enabling handles outside the canvas
     * @param type $productId
     * @return boolean
     */
    public function IsEnableHandles($productId) {
        $product = $this->_productLoader->create()->load($productId);
        $storeid = $this->_storeManager->getStore()->getId();
        $response = array();
        if ($product->getEnableHandles() != null) {
            $response['enable_handles'] = $product->getEnableHandles();
        } else {
            $response['enable_handles'] = $this->_pdHelper->getConfig('productdesigner/general/enable_handles', $storeid);
        }
        return ($response['enable_handles']) ? true : false;
    }

    public function getAllWebsites() {
        $value = $this->scopeConfig->getValue(self::XML_PATH_INSTALLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$value) {
            return array();
        }
        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $web = $this->scopeConfig->getValue(self::XML_PATH_WEBSITES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $websitesArray = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = array_diff($websitesArray, array(""));
        return $websites;
    }

    /**
     * Checks if product designer enabled or not
     * @param type $storeid
     * @return boolean
     */
    public function isEnable($storeid = 0) {
        $websiteId = $this->_storeManager->getStore()->getWebsite()->getId();
        $store = $this->_storeManager->getStore($storeid);
        $isenabled = $this->scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
        if ($isenabled) {
            if ($websiteId) {
                $websites = $this->getAllWebsites();
                $key = $this->scopeConfig->getValue(self::XML_PATH_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                if ($key == null || $key == '') {
                    return false;
                } else {
                    $en = $data = $this->scopeConfig->getValue(self::XML_PATH_EN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                    if ($isenabled && $en && in_array($websiteId, $websites)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $en = $data = $this->scopeConfig->getValue(self::XML_PATH_EN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                if ($isenabled && $en) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Check whether product designer is enabled for this product or not
     * @param type $product_id
     * @param type $storeid
     * @return int
     */
    public function isPdEnable($product_id, $storeid = 0, $isMagemobModule = false) {
        $product = ($isMagemobModule == false) ? $this->_productLoader->create()->load($product_id) : $this->_productModel->load($product_id);
        $status = $product->getStatus();
        /**
         * Check for product exist in website
         */
        $websiteid = $this->_storeManager->getStore()->getWebsiteId();
        $productExist = 0;
        if (in_array($websiteid, $product->getWebsiteIds())) {
            $productExist = 1;
        }

        /**
         * Check for enable_product_designer attribute
         */
        $pd_enable_attribute = $product->getResource()->getAttributeRawValue($product->getId(), 'enable_product_designer', $storeid);

        $isDesignAreaExist = $this->checkDesignArea($product, false, '');
        if ($pd_enable_attribute && $productExist && $status == 1 && $isDesignAreaExist) {
            return true;
        }
        return false;
    }

    public function isIntegrationPdEnable($product_id, $storeid = 0, $isMagemobModule = false) {
        $product = ($isMagemobModule == false) ? $this->_productLoader->create()->load($product_id) : $this->_productModel->load($product_id);
        $status = $product->getStatus();
        /**
         * Check for product exist in website
         */
        $websiteid = $this->_storeManager->getStore()->getWebsiteId();
        $productExist = 1;
        if (in_array($websiteid, $product->getWebsiteIds())) {
            $productExist = 1;
        }

        /**
         * Check for enable_product_designer attribute
         */
        $pd_enable_attribute = $product->getResource()->getAttributeRawValue($product->getId(), 'enable_product_designer', $storeid);

        $isDesignAreaExist = $this->checkDesignArea($product, false, '');
        if ($pd_enable_attribute && $productExist && $status == 1 && $isDesignAreaExist) {
            return true;
        }
        return false;
    }

    /**
     * It checks for image side and design area
     * @param type $product Main Product objects
     * @param type $isAccociated - pass true if we are checking image side and design area for associated product
     * @param type $parentProduct - pass Parent product if we are checking image side and design area for associated product
     * @return boolean
     */
    public function checkDesignArea($product, $isAccociated = false, $parentProduct = '') {
        $product_type = $product->getTypeId();
        $product_images = $product->getAllMediaGalleryImages();
        $imageSide = false;
        if ($product_type == 'configurable') {
            $defaultAssociatedProductId = $product->getDefaultAssociatedProduct();
            $defaultAssociatedProduct = $this->_productLoader->create()->load($defaultAssociatedProductId);
            $isDesignAreaExist = $this->checkDesignArea($defaultAssociatedProduct, true, $product);
            if ($defaultAssociatedProductId && $isDesignAreaExist) {
                return true;
            }
        } else {
            $imageSide = false;
            if ($product_images) {
                if (count($product_images) > 0) {
                    foreach ($product_images as $product_image) {
                        if ($product_image->getData('image_side_default')) {
                            $imageSide = true;
                        }
                    }
                }
            }
            $dimensions = $this->selectionAreaCollection->create()
            ->addFieldToFilter('product_id', $product->getId());
            if ($isAccociated && $parentProduct) {
                if (count($dimensions) <= 0) {
                    $dimensions = $this->selectionAreaCollection->create()
                    ->addFieldToFilter('product_id', $parentProduct->getId());
                }
                if ($parentProduct->getDesignerProductType()) {
                    $data_arr = array('productId' => $product->getId(),
                        'page' => 1,
                        'limit' => 3);
                    $product_images_collection = $this->getProductImageCollection($data_arr);
                    $product_images = $product_images_collection['templateCollection']->getData();
                    if (count($product_images) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($product->getDesignerProductType()) {
                    $data_arr = array('productId' => $product->getId(),
                        'page' => 1,
                        'limit' => 3);
                    $product_images_collection = $this->getProductImageCollection($data_arr);
                    $product_images = $product_images_collection['templateCollection']->getData();
                    if (count($product_images) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }

            $productStockObj = $this->stockRegistry->getStockItem($product->getId());
            if (count($dimensions) > 0 && $imageSide && $productStockObj->getIsInStock()) {
                return true;
            }
        }
        return false;
    }

    public function getProductOptions($productId, $selectedOptions, $designId) {
        $product = $this->_productLoader->create()->load($productId);
        $product_type = $product->getTypeId();
        if ($product_type == 'configurable') {
            $associatedProducts = $product->getTypeInstance()->getUsedProducts($product);
            if ($selectedOptions) {
                $defaultAssociatedProductId = $this->fetchSelectedAssociatedProduct($selectedOptions, $product);
            } else if ($designId) {
                $design = $this->designFactory->create()->load($designId);
                $defaultAssociatedProductId = $design->getAssociatedProductId();
            } else {
                $defaultAssociatedProductId = $product->getDefaultAssociatedProduct();
            }

            $attrs = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
            $allAttributeCodes = array();
            $allAttributeValues = array();
            /**
             * Fetch all attribute codes and labels from configurable products
             */
            foreach ($attrs as $attr) {
                $allAttributeCodes[$attr['attribute_id']] = ['label' => $attr['store_label'],
                'code' => $attr['attribute_code']];
                foreach ($attr['values'] as $val) {
                    $allAttributeValues[$val['value_index']] = $val['store_label'];
                }
            }
            $attrValues = array();
            $selectedOptions = array();
            $selectedOptionsValues = array();
            /**
             * Fetch associated product's attribute options and default product's options if proper configured
             */
            $productsCombination = array();
            foreach ($associatedProducts as $associated_product) {
                $productStockObj = $this->stockRegistry->getStockItem($associated_product->getId());
                $isDesignAreaExist = $this->checkDesignArea($associated_product, true, $product);
                if ($productStockObj->getIsInStock() && $isDesignAreaExist) {
                    $allValues = array();
                    foreach ($allAttributeCodes as $attributeId => $allAttributeCode) {
                        $attrValue = $associated_product->getData($allAttributeCode['code']);
                        $allValues[] = $attrValue;
                        $attrValues[$attributeId][] = $attrValue;
                        if ($defaultAssociatedProductId == $associated_product->getId()) {
                            $selectedOptions[$attributeId] = $associated_product->getData($allAttributeCode['code']);
                            $selectedOptionsValues[] = $associated_product->getData($allAttributeCode['code']);
                        }
                    }
                    $allValuesString = implode("_", $allValues);
                    $productsCombination[$associated_product->getId()] = $allValuesString;
                }
            }
            /**
             * Check and Fetch swatches from options 
             */
            $swatchesCol = $this->swatchCollection->create();
            $swatchesOptionIds = array();
            $swatchesIds = array();
            foreach ($swatchesCol as $swatchesC) {
                $swatchesOptionIds[] = $swatchesC->getOptionId();
                $swatchesIds[] = $swatchesC->getId();
            }
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $productOptions = array();
            /**
             * Fetch swatch url and colors if swatch is enabled
             */
            foreach ($attrValues as $attributeId => $attrVal) {
                $swatchesFlag = 0;
                $unique_Values = array_values(array_unique($attrVal));
                $swatches = [];
                foreach ($unique_Values as $value_index) {
                    if (in_array($value_index, $swatchesOptionIds)) {
                        $indexOfSwatch = array_search($value_index, $swatchesOptionIds);
                        $swatchValue = $this->swatchFactory->create()->load($swatchesIds[$indexOfSwatch])->getValue();
                        if (!empty($swatchValue)) {
                            if (strspn($swatchValue, '#') > 0 || strspn($swatchValue, '/') > 0) {
                                $swatchesFlag++;
                                if (strspn($swatchValue, '#') > 0) {
                                    $type = 'color';
                                } else if (strspn($swatchValue, '/') > 0) {
                                    $type = 'image';
                                    $swatchValue = $mediaUrl . "attribute/swatch/" . $swatchValue;
                                }
                                $swatches[] = ['value_index' => $value_index, 'type' => $type,
                                'value' => $swatchValue];
                            } else {
                                $swatches[] = ['value_index' => $value_index, 'type' => 'text',
                                'value' => $allAttributeValues[$value_index]];
                            }
                        } else {
                            $swatches[] = ['value_index' => $value_index, 'type' => 'text',
                            'value' => $allAttributeValues[$value_index]];
                        }
                    } else {
                        $swatches[] = ['value_index' => $value_index, 'type' => 'text',
                        'value' => $allAttributeValues[$value_index]];
                    }
                }
                $_hasSwatch = 0;
                if ($swatchesFlag == count($unique_Values)) {
                    $_hasSwatch = 1;
                }
                foreach ($swatches as $index => $swatch) {
                    if (!$_hasSwatch) {
                        $swatches[$index] = ['value_index' => $swatch['value_index'],
                        'type' => 'text',
                        'value' => $allAttributeValues[$swatch['value_index']],
                        'label' => $allAttributeValues[$swatch['value_index']]];
                    } else {
                        $swatches[$index] = ['value_index' => $swatch['value_index'],
                        'type' => $swatch['type'],
                        'value' => $swatch['value'],
                        'label' => $allAttributeValues[$swatch['value_index']]];
                    }
                }
                $productOptions[] = array('id' => $attributeId,
                    'label' => $allAttributeCodes[$attributeId]['label'],
                    'hasSwatch' => $_hasSwatch,
                    'values' => $swatches);
            }
            $response['productOptions'] = $productOptions;
            $response['selectedOptions'] = $selectedOptions;
            $response['selectedOptionsValues'] = $selectedOptionsValues;
            $response['productsCombination'] = $productsCombination;
        } else {
            $response['productOptions'] = array();
            $response['selectedOptions'] = array();
            $response['selectedOptionsValues'] = array();
            $response['productsCombination'] = array();
        }
        return $response;
    }

    /**
     * 
     * @param type $data
     * @param type $identifier
     */
    public function setCache($data, $identifier) {
     $identifier .= $this->_storeManager->getStore()->getBaseUrl();
     $this->cache->save($this->_serialize->serialize($data), $identifier, array(), $this->_cacheTimeLimit);
 }

    /**
     * 
     * @param type $identifier
     * @return type
     */
    public function loadCache($identifier) {
        $identifier .= $this->_storeManager->getStore()->getBaseUrl();
        $cacheResponse = $this->cache->load($identifier);
        if($cacheResponse) {
            $cacheResponse = $this->_serialize->unserialize($cacheResponse);   
        }
        return $cacheResponse;
    }

    /**
     * 
     * @param type $e exception
     * @param type $class Action class name
     */
    public function throwException($e, $class) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Productdesigner.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $response['status'] = 'failure';
        $response['log'] = "Error while " . $class . " - " . $e->getMessage();
        $response['message'] = "Something went wrong";
        $logger->info($response['log']);
        return $response;
    }

    public function generateImage($dataUrls, $product_image_data, $type, $hasDesign = true) {
        $canvasRatio = self::CanvasRatio;
        if ($dataUrls) {
            $source = $this->processDataURL($dataUrls);
        }
        list($iWidth, $iHeight) = getimagesize($product_image_data['path']);
        list($base_image_name, $generatedImageFullURL, $generatedImageFullPath, $destination, $imgtype) = $this->createImage($product_image_data['path'], $type);

        $dimensions = $product_image_data['dim'];
        foreach ($dimensions as $dimension) {
            if ($product_image_data['enable_handles']) {
                $clipx = $clipy = $canvasRatio;
                if ($dimension['height'] + $canvasRatio > $iHeight) {
                    $clipy = $iHeight / $dimension['height'];
                }
                if ($dimension['width'] + $canvasRatio > $iWidth) {
                    $clipx = $iWidth / $dimension['width'];
                }
            } else {
                $clipx = $clipy = 0;
            }
            $x1 = $dimension['x1'] - (($dimension['width'] + $clipx - $dimension['width']) / 2);
            $y1 = $dimension['y1'] - (($dimension['height'] + $clipy - $dimension['height']) / 2);
            $imageIdDesignAreaId = '@' . $dimension['image_id'] . '&' . $dimension['designareaId'];
            if (isset($source[$imageIdDesignAreaId])) {
                imagecopy($destination, $source[$imageIdDesignAreaId], $x1, $y1, 0, 0, imagesx($source[$imageIdDesignAreaId]), imagesy($source[$imageIdDesignAreaId]));
            }
        }
        imagesavealpha($destination, true);
        switch ($imgtype) {
            case 'image/jpeg':
            imagejpeg($destination, $generatedImageFullPath, 100);
            break;
            case 'image/gif':
            imagejpeg($destination, $generatedImageFullPath, 80);
            break;
            case 'image/png':
            imagepng($destination, $generatedImageFullPath);
            break;
            default:
            break;
        }
        /**
         * Generate watermark
         */
        if (($type['path'] == 'preview' || $type['path'] == 'download') && $hasDesign == true) {
            $this->_eventManager->dispatch('generate_images', ['destination' => $destination, 'generatedImageFullPath' => $generatedImageFullPath, 'imgtype' => $imgtype]);
        }
        $createSourceImage = $this->createSourceImage($generatedImageFullPath);
        $destination = $createSourceImage[1];
        imagesavealpha($destination, true);
        switch ($imgtype) {
            case 'image/jpeg':
            imagejpeg($destination, $generatedImageFullPath, 100);
            break;
            case 'image/gif':
            imagejpeg($destination, $generatedImageFullPath, 80);
            break;
            case 'image/png':
            imagepng($destination, $generatedImageFullPath);
            break;
            default:
            break;
        }

        return array('base' =>
            array('url' => $generatedImageFullURL,
                'path' => $generatedImageFullPath,
                'name' => $base_image_name));
    }

    public function processDataURL($dataUrls) {
        $images = array();
        foreach ($dataUrls as $key => $dataUrl) {
            $images[$key] = base64_decode($dataUrl);
        }
        $source = array();
        foreach ($images as $key => $value) {
            $source[$key] = imagecreatefromstring($value);
        }
        return $source;
    }

    public function convertRelToAbsPath($product_image) {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $baseDir = $this->directoryList->getRoot();
        /*
          Start :  replace base url with base dir
         */
          $prod_image_path = str_replace($baseUrl, $baseDir . DIRECTORY_SEPARATOR, $product_image);
        /*
          END :  replace base url with base dir
         */
          return $prod_image_path;
      }

      public function createImage($product_image_path, $type) {
        if ($type['path'] == 'preview') {
            $folder = 'productdesigner' . DIRECTORY_SEPARATOR . 'preview';
        } else if ($type['path'] == 'download') {
            $folder = 'productdesigner' . DIRECTORY_SEPARATOR . 'download';
        } else {
            $folder = 'productdesigner' . DIRECTORY_SEPARATOR . $type['path'] . DIRECTORY_SEPARATOR . $type['id'] . DIRECTORY_SEPARATOR . 'base';
        }
        $generatedImagePath = $this->mediaPath . $folder;
        $generatedImageURL = $this->mediaURL . $folder;
        if (!file_exists($generatedImagePath)) {
            mkdir($generatedImagePath, 0777, true);
        }
        list($base_image_name, $destination, $imgtype) = $this->createSourceImage($product_image_path);
        $generatedImageFullPath = $generatedImagePath . DIRECTORY_SEPARATOR . $base_image_name;
        $generatedImageFullURL = $generatedImageURL . DIRECTORY_SEPARATOR . $base_image_name;
        return array($base_image_name, $generatedImageFullURL, $generatedImageFullPath, $destination, $imgtype);
    }

    public function createSourceImage($product_image_path) {
        $imageInfo = getimagesize($product_image_path);
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $imgtype = image_type_to_mime_type($imageInfo[2]);
        switch ($imgtype) {
            case 'image/jpeg':
            $base_image_name = "pd_" . $time . ".jpg";
            $destination = imagecreatefromjpeg($product_image_path);
            break;
            case 'image/gif':
            $base_image_name = "pd_" . $time . ".gif";
            $destination = imagecreatefromgif($product_image_path);
            break;
            case 'image/png':
            $base_image_name = "pd_" . $time . ".png";
            $destination = imagecreatefrompng($product_image_path);
            break;
            default:
            break;
        }
        return array($base_image_name, $destination, $imgtype);
    }

    public function joinObject($selectedOptions) {
        return implode("_", array_keys($selectedOptions)) . '_' . implode("_", array_values($selectedOptions));
    }

    /**
     * 
     * @param type $product_image_path
     */
    public function calculateResizeWidthHeight($product_image_path) {
        list($iWidth, $iHeight) = getimagesize($product_image_path);
        if ($iWidth > $iHeight) {
            $resize_height = (self::ResizeWidth * $iHeight) / $iWidth;
            $resize_width = self::ResizeWidth;
        } else {
            $resize_height = self::ResizeHeight;
            $resize_width = (self::ResizeHeight * $iWidth) / $iHeight;
        }
        return array($resize_width, $resize_height);
    }

    public function getJsonPrice($productID, $currentCurrencyCode) {
        $config = array();

        $_request = $this->calculation->getRateRequest(false, false, false);
        $obj_product = $this->_productLoader->create();
        $product = $obj_product->load($productID);
        $store = $this->_storeManager->getStore();
        $storeId = $store->getId();

        $_finalPrice = $this->directoryHelper->currencyConvert($product->getFinalPrice(), $this->_storeManager->getStore()->getBaseCurrencyCode(), $currentCurrencyCode);

        $addedTextPrice = (!empty($product->getAddedTextPrice())) ? $product->getAddedTextPrice() : $this->_pdHelper->getConfig('productdesigner/general/price_per_text', $storeId);

        $addedImagePrice = (!empty($product->getAddedImagePrice())) ? $product->getAddedImagePrice() : $this->_pdHelper->getConfig('productdesigner/general/price_per_image', $storeId);

        $customAddedImagePrice = (!empty($product->getCustomAddedImagePrice())) ? $product->getCustomAddedImagePrice() : $this->_pdHelper->getConfig('productdesigner/general/price_per_custom_image', $storeId);

        $additionalFixedPrice = (!empty($product->getCustomizedProductsPrice())) ? $product->getCustomizedProductsPrice() : $this->_pdHelper->getConfig('productdesigner/general/design_area_fixed_price', $storeId);

        $priceFormat = $this->localeFormat->getPriceFormat();
        $currentCurrencySymbol = $this->currencyFactory->create()->load($currentCurrencyCode)->getCurrencySymbol();
        if (!$currentCurrencySymbol) {
            $currentCurrencySymbol = $currentCurrencyCode;
        }
        $priceFormat['pattern'] = $currentCurrencySymbol . "%s";
        $config = array(
            'priceFormat' => $priceFormat,
            'productPrice' => $this->priceHelper->currency($_finalPrice, false, false),
            'addedTextPrice' => $addedTextPrice,
            'addedImagePrice' => $addedImagePrice,
            'customAddedImagePrice' => $customAddedImagePrice,
            'additionalFixedPrice' => $additionalFixedPrice
        );
        return $config;
    }

    public function saveDesign($params) {
        $this->design->setData($params);
        $this->design->save();
        return $this->design->getDesignId();
    }

    public function saveGenerateImages($params) {
        if (isset($params['images'])) {
            foreach ($params['images'] as $image_key => $design_image) {
                $designImagesModel = $this->designImagesFactory->create();
                $designImagesModel->setDesignId($params['designId'])
                ->setDesignImageType($image_key)
                ->setProductImageId($params['image_id'])
                ->setImagePath(str_replace('\\', '/', $design_image['name']));
                $designImagesModel->save();
            }
        }
        if (isset($params['svg_images'])) {
            foreach ($params['svg_images'] as $design_images) {
                foreach ($design_images as $image_key => $design_image) {
                    $designImagesModel = $this->designImagesFactory->create();
                    $designImagesModel->setDesignId($params['designId'])
                    ->setDesignImageType($image_key)
                    ->setProductImageId($params['image_id'])
                    ->setImagePath(str_replace('\\', '/', $design_image['name']));
                    $designImagesModel->save();
                }
            }
        }
    }

    public function fetchCatalogProducts($catId, $store, $search, $limit) {
        $image = 'category_page_list';
        if (isset($catId)) {
            $category = $this->_catalogModel->load($catId);
            $products = $category->getProductCollection()->setStore($store);
        } else {
            $products = $this->_productCollectionFactory->create();
        }
        $products->addAttributeToSelect('*')->addAttributeToFilter('visibility', array("neq" => 1));
        $products->addAttributeToFilter('status', array("eq" => 1));
        $products->addAttributeToFilter('type_id', array("in" => array('simple', 'configurable')));
        $products->addAttributeToFilter('enable_product_designer', array("eq" => 1));
        $products->addAttributeToFilter('name', array('like' => '%' . $search . '%'));
        $products->getSelect()->group('entity_id');
        $this->_stockFilter->addInStockFilterToCollection($products);

        $productsresponse = [];
        $productsdata = [];
        $counter = 0;
        $productsresponse['loadMore'] = false;
        foreach ($products as $_product) {
            if ($this->isPdEnable($_product->getId())) {
                $counter++;
                if ($counter <= $limit) {
                    $defaultAssociatedProduct = $defaultAssociatedProductId = $templateId = $defaultAssociatedProductTemplateId = "";
                    $product_type = $_product->getTypeId();
                    if ($product_type == "configurable") {
                        $defaultAssociatedProductId = $_product->getDefaultAssociatedProduct();
                        $defaultAssociatedProduct = $this->_productLoader->create()->load($defaultAssociatedProductId);
                        if (!empty($defaultAssociatedProductId) && !empty($defaultAssociatedProduct->getPreLoadedTemplate())) {
                            $defaultAssociatedProductTemplateId = base64_encode($defaultAssociatedProduct->getPreLoadedTemplate());
                        }
                    }
                    $productTemplateId = base64_encode($_product->getPreLoadedTemplate());
                    /* $_product = (!empty($defaultAssociatedProductTemplateId)) ? $defaultAssociatedProduct : $_product; */
                    $templateId = (!empty($defaultAssociatedProductTemplateId)) ? $defaultAssociatedProductTemplateId : $productTemplateId;
                    $templateUrl = "";
                    $href = $this->_storeManager->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $_product->getId();
                    $product_image = $this->_imagehelper->init($_product, $image)->resize(100)->getUrl();
                    $makeDesignUrl = $href;

                    if (!empty($templateId)) {
                        $href = $this->_storeManager->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $_product->getId() . '/template/' . $templateId;
                    }

                    $productsdata[] = array(
                        'id' => $_product->getId(),
                        'name' => $_product->getName(),
                        'url' => $product_image,
                        'href' => $href,
                    );
                } else {
                    $productsresponse['loadMore'] = true;
                }
            }
        }
        $productsresponse['products'] = $productsdata;
        return $productsresponse;
    }

    public function loadDesign($designId) {
        $design = $this->design->load($designId)->getData();
        $unsetArray = array(
            'created_at',
            'customer_comments',
            'customer_id',
            'product_id',
            'text_image_file',
            'updated_at',
        );
        foreach ($unsetArray as $unsetVal) {
            unset($design[$unsetVal]);
        }
        return $design;
    }

    /**
     * 
     * @param type $product_id
     * @return type
     */
    public function getProductTypeAndMediaImages($product_id) {
        $product = $this->_productLoader->create()->load($product_id);
        $product_type = $product->getTypeId();
        if ($product_type == 'configurable') {
            $childProducts = $this->configurable->getChildrenIds($product_id);
            $grouped_product_images = array();
            foreach ($childProducts[0] as $childProduct) {
                $childProduct = $this->_productLoader->create()->load($childProduct);
                foreach ($childProduct->getAllMediaGalleryImages()->getItems() as $_image) {
                    $grouped_product_images[] = $_image;
                }
            }
        } else {
            $grouped_product_images = $product->getAllMediaGalleryImages()->getItems();
        }
        $media_images = array();
        foreach ($grouped_product_images as $grouped_product_image) {
            $media_images[$grouped_product_image->getValueId()] = $grouped_product_image->getData();
        }
        $data['media_image'] = $media_images;
        $data['product'] = $product;
        $data['product_type'] = $product_type;
        return $data;
    }

    public function getAllImageSides() {
        $allImageSides = $this->sideFactory->create()->getCollection()->getData();
        $imageSideData = array();
        foreach ($allImageSides as $imageSide) {
            $imageSideData[$imageSide['imageside_id']] = $imageSide['imageside_title'];
        }
        return $imageSideData;
    }

    public function getImageSideFromTemplateMedia($productImageId, $type = false, $_mediaGallery, $_side) {
        if (!$type) {
            $mediaGallery = $_mediaGallery->create()->addFieldToFilter('value_id', $productImageId);
            $mediaGalleryData = ($mediaGallery->getData()[0]);
            $imageSide = $mediaGalleryData['image_side'];
            $sidecollection = $_side->load($imageSide);
            return $sidecollection->getImagesideTitle();
        }
    }

}
