<?php

namespace Biztech\Productdesigner\Controller\Text;

class getFonts extends \Magento\Framework\App\Action\Action {

    protected $_storeManager;
    protected $_helper;
    protected $_assetRepo;
    protected $_dir;
    protected $request;
    protected $_logger;
    protected $_infoHelper;
    protected $fontsFactory;
    protected $fontsCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Data $helper, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\Filesystem\DirectoryList $dir, \Magento\Framework\App\Request\Http $request, \Psr\Log\LoggerInterface $logger, \Biztech\Productdesigner\Model\FontsFactory $fontsFactory, \Biztech\Productdesigner\Model\Mysql4\Fonts\CollectionFactory $fontsCollection
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_assetRepo = $assetRepo;
        $this->_dir = $dir;
        $this->request = $request;
        $this->_logger = $logger;
        $this->_infoHelper = $infoHelper;
        $this->fontsFactory = $fontsFactory;
        $this->fontsCollection = $fontsCollection;
        parent::__construct($context);
    }

    public function execute() {

        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $page = !empty($data['page']) ? $data['page'] : "";
            $limit = 20;
            $cacheKey = 'getFonts' . $page;
            $response = $this->_infoHelper->loadCache($cacheKey);
            if (!$response) {
                $storeid = $this->_storeManager->getStore()->getId();
                $defaultFontFamilyID = $this->_helper->getConfig('productdesigner/text_general/default_font_family', $storeid);
                $defaultFontFamilyData = $this->fontsFactory->create()->load($defaultFontFamilyID)->getData();
                $defaultFontSize = $this->_helper->getConfig('productdesigner/text_general/default_font_size', $storeid);
                $textLimitEnable = $this->_helper->getConfig('productdesigner/text_general/text_limit', $storeid);
                $textLimitCounter = $this->_helper->getConfig('productdesigner/text_general/text_limit_counter', $storeid);
                $limitAlert = htmlspecialchars_decode($this->_helper->getConfig('productdesigner/text_general/limit_alert', $storeid));
                $allstore = array();
                array_push($allstore, 0);
                array_push($allstore, $storeid);
                $getTotalRecord = $this->fontsCollection->create()
                ->addFieldToFilter('status', array('eq' => '1'))->count();
                $fontsCollections = $this->fontsCollection->create()
                ->addFieldToFilter('status', array('eq' => '1'))
                ->setCurPage($page)
                ->setPageSize($limit)
                ->setOrder('position', 'ASC');
                $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $fonts_data = [];
                foreach ($fontsCollections as $fontsCollection) {
                    $font_image = ($fontsCollection->getData('font_image')) ? ($mediaUrl . $fontsCollection->getData('font_image')) : '';
                    $fonts_data[] = array(
                        'font_id' => $fontsCollection->getData('fonts_id'),
                        'font_label' => $fontsCollection->getData('font_label'),
                        'font_image' => $font_image,
                        'font_file' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $fontsCollection->getData('font_file'),
                    );
                }                

                if ($getTotalRecord <= ($limit * $page)) {
                    $loadMoreFlag = 0;
                } else {
                    $loadMoreFlag = 1;
                }
                if ($defaultFontFamilyData) {
                    $fontFamilyData = array(
                        'font_id' => $defaultFontFamilyData['fonts_id'],
                        'font_label' => $defaultFontFamilyData['font_label'],
                        'font_file' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $defaultFontFamilyData['font_file'],
                    );
                } else {
                    $fontFamilyData = [];
                }

                $response['fonts'] = $fonts_data;
                $response['loadMoreFlag'] = $loadMoreFlag;
                $response['defaultFontFamily'] = $fontFamilyData;
                $response['defaultFontSize'] = $defaultFontSize;
                $response['textLimitEnable'] = $textLimitEnable;
                $response['textLimitCounter'] = $textLimitCounter;
                $response['limitAlert'] = $limitAlert;

                $this->_infoHelper->setCache($response, $cacheKey);
            }
            // if forceLoadFont flag is set
            if(isset($data['fontList'])) {
                $loadTheseFonts = $data['fontList'];
                $forceLoadFontCollection = $this->fontsCollection->create()
                ->addFieldToFilter('font_label', array('in' => $loadTheseFonts));
                $myFonts = array();
                $fonts_data = array();
                foreach ($forceLoadFontCollection as $fontsCollection) {
                    $font_image = ($fontsCollection->getData('font_image')) ? ($mediaUrl . $fontsCollection->getData('font_image')) : '';
                    $response['fonts'][] = array(
                        'font_id' => $fontsCollection->getData('fonts_id'),
                        'font_label' => $fontsCollection->getData('font_label'),
                        'font_image' => $font_image,
                        'font_file' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $fontsCollection->getData('font_file'),
                    );
                }
                // $response['fonts'] = array_merge($fonts_data);
            }
            $this->getResponse()->setBody(json_encode($response));
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
