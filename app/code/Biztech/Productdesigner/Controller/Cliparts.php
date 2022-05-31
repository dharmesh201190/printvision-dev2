<?php
namespace Biztech\Productdesigner\Controller;

abstract class Cliparts extends \Magento\Framework\App\Action\Action {

    protected $_infoHelper;
    protected $_productLoader;
    protected $_clipartFactory;
    protected $_clipartMediaFactory;
    protected $_storeManager;
    protected $_assetRepo;
    protected $clipArtcategories;
    protected $_pdHelper;
    protected $allstore = array();
    protected $_localeFormat;
    protected $_dir;
    protected $_currencyFactory;
    protected $_clipart;
    protected $_eventManager;
    protected $_objectFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Catalog\Model\ProductFactory $_productLoader, \Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\View\Asset\Repository $assetRepo, \Biztech\Productdesigner\Helper\Data $dataHelper, \Biztech\Productdesigner\Model\Mysql4\Clipartmedia\CollectionFactory $ClipArtMedia, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Framework\App\Filesystem\DirectoryList $dir, \Magento\Directory\Model\CurrencyFactory $currencyFactory, \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->_infoHelper = $infoHelper;
        $this->_productLoader = $_productLoader;
        $this->_clipartFactory = $clipartFactory;
        $this->_storeManager = $storeManager;
        $this->_assetRepo = $assetRepo;
        $this->_clipartMediaFactory = $ClipArtMedia;
        $this->_pdHelper = $dataHelper;
        $this->_localeFormat = $localeFormat;
        $this->_dir = $dir;
        $this->_currencyFactory = $currencyFactory;
        $this->_eventManager = $manager;
        $this->_objectFactory = $objectFactory;
        parent::__construct($context);
    }

    public function fetchClipartImages($clipart_id, $limit = 12, $searchText = "", $page = 1) {
        try {

            $clipartMedia_json = $this->_clipartMediaFactory->create();
            if (!empty($searchText)) {
                $clipartMedia_json = $clipartMedia_json->addFieldToFilter(
                        array('tags', 'label'), array(
                    array('like' => '%' . $searchText . '%'),
                    array('like' => '%' . $searchText . '%'),
                        )
                );
            }
            $clipartMedia_json = $clipartMedia_json->addFieldToFilter('clipart_id', $clipart_id);
            
            $dirImage = $this->_dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . "/productdesigner/clipart/";

            $clipartMedia_json = $clipartMedia_json->addFieldToFilter('disabled', 0);
            $getTotalRecord = $clipartMedia_json->count();

            $clipartMedia_json = $clipartMedia_json
                    ->setCurPage($page)
                    ->setPageSize($limit)
                    ->setOrder('position', 'ASC');

            $getClipartData = $clipartMedia_json->getData();

            $response = array();
            $returnData = array();
            $path = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );

            $priceFormat = $this->_localeFormat->getPriceFormat();
            $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
            $currentCurrencySymbol = $this->_currencyFactory->create()->load($currentCurrencyCode)->getCurrencySymbol();
            if (!$currentCurrencySymbol) {
                $currentCurrencySymbol = $currentCurrencyCode;
            }
            $priceFormat['pattern'] = $currentCurrencySymbol . "%s";
            foreach ($getClipartData as $value) {
                $imgPath = $path . 'productdesigner/clipart/resized' . $value['image_path'];
                if(!file_exists($imgPath)){
                    $imgPath = $path . 'productdesigner/clipart/' . $value['image_path'];
                }
                $midiumImgPath = $path . 'productdesigner/clipart' . $value['image_path'];
                if (file_exists($dirImage . $value['image_path'])) {
                    $returnData[] = [
                        "label" => $value['label'],
                        "image_path" => $imgPath,
                        "medium_url" => $midiumImgPath,
                        "price" => $value['price'],
                        "clipart_id" => $value['clipart_id'],
                        "image_id" => $value['image_id'],
                        "tags" => $value['tags']
                    ];
                }
            }

            $totalClipartImages = count($returnData);

            if (($limit * $page) >= $getTotalRecord  || $totalClipartImages <= 0) {
                $loadMoreFlag = 0;
            } else {
                $loadMoreFlag = 1;
            }

            $response['priceFormat'] = $priceFormat;
            $response['images'] = $returnData;
            $response['loadMoreFlag'] = $loadMoreFlag;
            $response['page'] = $page;
            return $response;
        } catch (\Exception $e) {
            $response = $this->_infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

}
