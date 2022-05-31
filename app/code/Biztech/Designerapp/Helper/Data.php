<?php

namespace Biztech\Designerapp\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $urlBuilder;
	protected $_productLoader;
	protected $_storeManager;
	protected $_infoHelper;
	protected $request;
	protected $_pdHelper;
	protected $_filesystem;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Biztech\Productdesigner\Helper\Info $infoHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlBuilder,
		\Magento\Catalog\Model\ProductFactory $_productLoader,
		\Magento\Framework\App\Request\Http $request,
		\Biztech\Productdesigner\Helper\Data $dataHelper,
		\Magento\Framework\Filesystem $filesystem
	) {
		$this->_infoHelper = $infoHelper;
		$this->_storeManager = $storeManager;
		$this->urlBuilder = $urlBuilder;
		$this->_productLoader = $_productLoader;
		$this->request = $request;
		$this->_pdHelper = $dataHelper;
		$this->_filesystem = $filesystem;
		parent::__construct($context);
	}

	public function getByiUrl($id, $preventIsAppParam = false) {
		if($this->isEnable($id) == false) {
			return "";
		}
		// prepare default url first
		$url = $this->urlBuilder->getUrl('productdesigner', array('_secure' => $this->request->isSecure())) . 'index/index/id/' . $id;

		// load product instance by id
		$product = $this->_productLoader->create()->load($id);		

		// init
		$preloadedTemplateId = $this->checkForPreloaded($product);		

		if($preloadedTemplateId) {
			$url .= "/template/" . $preloadedTemplateId;
		}

		if($preventIsAppParam == false) {
			$url .= '/isApp/true';
		}
		return $url;
	}

	public function isEnable($productId) {
		$store = $this->_storeManager->getStore();
		$storeId = $store->getId();
		$isEnable = $this->_infoHelper->isEnable($storeId);
		$isPdEnable = $this->_infoHelper->isPdEnable($productId,$storeId);
		return (isset($isEnable) && $isEnable && isset($isPdEnable) && $isPdEnable) ? true : false;
	}

	public function isEnableByStore() {
		$store = $this->_storeManager->getStore();
		$storeId = $store->getId();
		$isEnable = $this->_infoHelper->isEnable($storeId);
		return (isset($isEnable) && $isEnable) ? true : false;
	}

	public function isEnableCategory(){
		$store = $this->_storeManager->getStore();
		$storeId = $store->getId();
		$designItEnable = $this->_pdHelper->getConfig('productdesigner/general/enabled_design_btn_category', $storeId);
		return (isset($designItEnable) && $designItEnable) ? true : false;
	}

	public function isAddToCart() {
		$store = $this->_storeManager->getStore();
		$storeId = $store->getId();
		$addToCartEnable = $this->_pdHelper->getConfig('productdesigner/general/disable_addtocart_btn_category', $storeId);
		return (isset($addToCartEnable) && $addToCartEnable) ? true : false;
	}

	public function setPreloadedTemplateImage($productId) {
		// load product instance by id
		$product = $this->_productLoader->create()->load($productId);		

		// init
		$preloadedTemplateId = $this->checkForPreloaded($product);		
		$preloadedImage = '';
		if($preloadedTemplateId) {
			$preloadedTemplateIdDecoded = base64_decode($preloadedTemplateId);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$designTemplateModel = $objectManager->get('Biztech\DesignTemplates\Model\Designtemplates');
			$designTemplateData = $designTemplateModel->load($preloadedTemplateIdDecoded);

			$image = $designTemplateData->getImage();
			$mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
			$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$preloadedImage = $mediapath . 'productdesigner' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $preloadedTemplateIdDecoded . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . $image;
			if(file_exists($preloadedImage)) {
				$preloadedImage = $mediaUrl . 'productdesigner' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $preloadedTemplateIdDecoded . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . $image;
			} else {
				$preloadedImage = '';
			}
		}

		return $preloadedImage;
	}

	public function checkForPreloaded($product)	{
		// check if product is having preloaded template
		$isPreLoaded = $product->getPreLoadedTemplate();

		$defaultAssociatedProductTemplateId = "";
		if($isPreLoaded && !empty($isPreLoaded)) {
			$defaultAssociatedProductTemplateId = base64_encode($isPreLoaded);			
			$product_type = $product->getTypeId();
			if($product_type == "configurable") {
				$defaultAssociatedProductId = $product->getDefaultAssociatedProduct();
				$defaultAssociatedProduct = $this->_productLoader->create()->load($defaultAssociatedProductId);
				if (!empty($defaultAssociatedProductId) && !empty($defaultAssociatedProduct->getPreLoadedTemplate())) {
					$defaultAssociatedProductTemplateId = base64_encode($defaultAssociatedProduct->getPreLoadedTemplate());
				}
			}
		}
		return $defaultAssociatedProductTemplateId;
	}
}