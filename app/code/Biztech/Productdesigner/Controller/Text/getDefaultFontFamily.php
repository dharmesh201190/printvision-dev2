<?php

namespace Biztech\Productdesigner\Controller\Font;
class getDefaultFontFamily extends \Magento\Framework\App\Action\Action {

	protected $_storeManager;
	protected $fontsFactory;
	protected $fontsCollection;

	public function __construct(
		\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Model\FontsFactory $fontsFactory, \Biztech\Productdesigner\Model\Mysql4\Fonts\CollectionFactory $fontsCollection
	) {
		$this->_storeManager = $storeManager;
		$this->fontsFactory = $fontsFactory;
		$this->fontsCollection = $fontsCollection;
		parent::__construct($context);
	}

	public function execute() {
		try {
			$data = json_decode(file_get_contents('php://input'), TRUE);
			$cacheKey = 'getDefaultFontFamily';
			$response = $this->_infoHelper->loadCache($cacheKey);

			if (!$response) {
				$data = $this->getRequest()->getParams();
				$defaultFontFamilyID = $data['defaultFontFamily'];
				$storeid = $this->_storeManager->getStore()->getId();
				$defaultFontFamilyData = $this->fontsFactory->create()->load($defaultFontFamilyID)->getData();
				$path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				$storeIds = explode(",", $defaultFontFamilyData['store_id']);
				if ((!in_array($storeid, $storeIds) && !in_array(0, $storeIds)) || $defaultFontFamilyData['status'] == 2) {
					$allstore = array();
					array_push($allstore, 0);
					array_push($allstore, $storeid);
					$fontsCollections = $this->fontsCollection->create()
						->addFieldToFilter('status', array('eq' => '1'))
						->addStoreFilter($allstore)
						->setOrder('position', 'ASC');
					$defaultFontFamilyData = $fontsCollections->getFirstItem()->getData();
				}
				if ($defaultFontFamilyData) {
					$response['defaultFontFamily'] = array(
						'font_id' => $defaultFontFamilyData['fonts_id'],
						'font_label' => $defaultFontFamilyData['font_label'],
						'font_image' => $path . $defaultFontFamilyData['font_image'],
						'status' => $defaultFontFamilyData['status'],
						'position' => $defaultFontFamilyData['position'],
						'font_file' => "pub/media/" . $defaultFontFamilyData['font_file'],
					);
				} else {
					$response['defaultFontFamily'] = [];
				}
				$this->_infoHelper->setCache($response, $cacheKey);
			}
			$this->getResponse()->setBody(json_encode($response));
		}catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
	}

}
