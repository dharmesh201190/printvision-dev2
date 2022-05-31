<?php

namespace Biztech\Productdesigner\Helper;
use \Magento\Framework\Serialize\Serializer\Serialize;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_INSTALLED = 'productdesigner/activation/installed';
    const XML_PATH_DATA = 'productdesigner/activation/data';
    const XML_PATH_WEBSITES = 'productdesigner/activation/websites';
    const MediumImageWidth = 540;
    const ResizeImageWidth = 90;

    protected $_encryptor;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_imageFactory;
    protected $designCollection;
    protected $productModel;
    protected $designImageCollection;
    protected $dataInterface;
    protected $designFactory;
    protected $designImageFactory;
    protected $_filesystem;
    protected $customerImagesCollectionFactory;
    protected $_serialize;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Image\AdapterFactory $imageFactory, \Biztech\Productdesigner\Model\Mysql4\Designs\CollectionFactory $designCollection, \Magento\Catalog\Model\ProductFactory $productModel, \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designImageCollection, \Magento\Framework\App\ProductMetadataInterface $dataInterface, \Biztech\Productdesigner\Model\DesignsFactory $designFactory, \Biztech\Productdesigner\Model\DesignimagesFactory $designImageFactory, \Magento\Framework\Filesystem $filesystem,\Biztech\Productdesigner\Model\Mysql4\Customerimages\CollectionFactory $customerImagesCollectionFactory, Serialize $serialize
    ) {
        $this->_storeManager = $storeManager;
        $this->_encryptor = $encryptor;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_imageFactory = $imageFactory;
        $this->designCollection = $designCollection;
        $this->productModel = $productModel;
        $this->designImageCollection = $designImageCollection;
        $this->dataInterface = $dataInterface;
        $this->designFactory = $designFactory;
        $this->designImageFactory = $designImageFactory;
        $this->_filesystem = $filesystem;
        $this->customerImagesCollectionFactory = $customerImagesCollectionFactory;
        $this->_serialize = $serialize;
        parent::__construct($context);
    }

    public function getAllStoreDomains() {
        $domains = array();
        foreach ($this->_storeManager->getWebsites() as $website) {
            $unSecureUrl = $website->getConfig('web/unsecure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $unSecureUrl))) {
                $domains[] = $domain;
            }
            $secureUrl = $website->getConfig('web/secure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $secureUrl))) {
                $domains[] = $domain;
            }
        }
        return array_unique($domains);
    }

    public function getDataInfo() {

        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return json_decode(base64_decode($this->_encryptor->decrypt($data)));
    }

    public function getFormatUrl($url) {
        $input = trim($url, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        if (isset($urlParts['path'])) {
            $domain = preg_replace('/^www\./', '', $urlParts['host'] . $urlParts['path']);
        } else {
            $domain = preg_replace('/^www\./', '', $urlParts['host']);
        }
        return $domain;
    }

    public function getConfig($data, $storeid = '') {
        if ($storeid) {
            $store = $this->_storeManager->getStore($storeid);
            return $this->_scopeConfig->getValue($data, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
        } else {
            return $this->_scopeConfig->getValue($data, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
    }

    public function resizeImage($path, $result, $resizeWidth = 90, $size = 'resized') {
        $file_ext = pathinfo($result['file'], PATHINFO_EXTENSION);
        if ($file_ext != 'svg') {
            list($width, $height) = getimagesize($path . $result['file']);
            if ($width > $height) {
                $ratio = $width / $height;
                $nWidth = $resizeWidth;
                $nHeight = $resizeWidth / $ratio;
            } else {
                $ratio = $height / $width;
                $nHeight = $resizeWidth;
                $nWidth = $nHeight / $ratio;
            }
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($path . $result['file']);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($nWidth, $nHeight);
            $destination = $path . $size . "/" . $result['file'];
            $imageResize->save($destination);
        } else {
            $explodePaths = explode("/", $result['file']);
            $explodePathString = $explodePaths[1] . "/" . $explodePaths[2];
            if (!file_exists($path . $size . "/" . $explodePathString)) {
                mkdir($path . $size . "/" . $explodePathString, 0777, true);
            }
            copy($path . $result['file'], $path . $size . "/" . $result['file']);
        }
    }

    public function checkForDirectory($directoryPath, $baseDirectoryName = 'clipart') {

        // flag value that will be changed once operation is done
        $flag = false;

        // Process begins
        do {

            // Store last position of directory separator
            $pos = strrpos($directoryPath, '/');

            // fetch full path till directory of image
            $directoryPath = substr($directoryPath, 0, $pos);

            // find last occurence of directory separator
            $pos = strrpos($directoryPath, '/') + 1;

            // find directory name
            $pathName = substr($directoryPath, $pos);

            // Check if directory is 
            if (is_dir($directoryPath) && ($pathName != $baseDirectoryName && $pathName != 'resized' && $pathName != 'medium')) {

                // check for available files(excluding system hidden folders)
                $files = array_diff(scandir($directoryPath), array('..', '.'));

                // if file count is 0, that indicates folder is empty, will remove the same
                if (count($files) == 0) {
                    rmdir($directoryPath);
                }

                // check if path has reached to base image path which indicates that our operation is complete
            } else if (($pathName == $baseDirectoryName || $pathName == 'resized') && $pathName != 'medium') {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            } else if ($pathName == $baseDirectoryName || $pathName == 'medium') {

                // this will indicate that our opearation is complete and traversal can be stopped now
                $flag = true;
            }
        } while ($flag == false);
    }

    public function getCustomerDesign($data) {
        $customerid = $data['customer_id'];
        $searchText = $data['searchText'];
        $page = $data['page'];
        $limit = $data['limit'];
        $collection = $this->designCollection->create()
                ->addFieldToFilter('customer_id', Array('eq' => $customerid))
                ->addFieldToFilter('is_display', Array('eq' => 1));
        if ($searchText != "") {
            $collection->addFieldToFilter(
                    array('title'), array(
                array('like' => '%' . $searchText . '%')
                    )
            );
        }
        $collection->setOrder('design_id', 'DESC');

        $totalRecords = count($collection->getData());

        $collection->setPageSize($limit);
        $collection->setCurPage($page);
        $collection->load();
        $designsdata = [];
        $designsAllData = [];
        foreach ($collection->getData() as $mydesign) {
            $design_id = $mydesign['design_id'];
            $product_id = $mydesign['product_id'];
            $title = $mydesign['title'];
            $product = $this->productModel->create()->load($product_id);
            $status = $product->getStatus();
            if ($status == 1) {
                $designImages = $this->designImageCollection->create()
                                ->addFieldToFilter('design_id', Array('eq' => $design_id))
                                ->addFieldToFilter('design_image_type', 'base')
                                ->getFirstItem()->getData();

                if (isset($designImages) && count($designImages) > 0) {
                    $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/designs/' . $design_id . '/base/' . $designImages['image_path'];

                    $designsdata[] = array(
                        'id' => $design_id,
                        'product_id' => $product_id,
                        'title' => $title,
                        'path' => $path,
                    );
                }
            }
        }

        if ($limit <= $totalRecords) {
            $loadMoreFlag = 1;
        } else {
            $loadMoreFlag = 0;
        }
        $totalpages = ceil($totalRecords / $limit);
        if ($page == $totalpages) {
            $loadMoreFlag = 0;
        }
        $designsAllData['designdata'] = $designsdata;
        $designsAllData['loadMoreFlag'] = $loadMoreFlag;
        $designsAllData['page'] = $page;
        $designsAllData['totalpages'] = $totalpages;
        return $designsAllData;
    }

    public function unserializeData($value) {
        $string = '';
        if (version_compare($this->dataInterface->getVersion(), '2.2.0', '>=')) {
            $string = json_decode($value, true);
        } else {
            // $string = \Magento\Framework\Serialize\SerializerInterface::unserialize($value);
            $string = (isset($value) && $value) ? $this->_serialize->unserialize($value) : '';
        }
        return $string;
    }

    public function convertImage($ext, $path, $name, $newname = NULL) {
        $exploded = explode('.', $name);
        $extoriginal = $exploded[count($exploded) - 1];
        switch ($extoriginal) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($path . $name);
                break;
            case 'png':
                $image = imagecreatefrompng($path . $name);
                break;
            case 'gif':
                $image = imagecreatefromgif($path . $name);
                break;
            case 'bmp':
                $image = imagecreatefrombmp($path . $name);
                break;
        }

        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100;

        $newname = ($newname == NULL) ? $exploded[0] : $newname;

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $newimage = $path . $newname . ".jpg";
                imagejpeg($bg, $newimage, $quality);
                break;
            case 'png':
                $newimage = $path . $newname . ".png";
                imagepng($bg, $newimage);
                break;
            case 'gif':
                $newimage = $path . $newname . ".gif";
                imagegif($bg, $newimage, $quality);
                break;
            case 'bmp':
                $newimage = $path . $newname . ".bmp";
                imagewbmp($bg, $newimage, $quality);
                break;
        }
        imagedestroy($bg);
        return $newimage;
    }

    public function deleteMyDesign($design_id) {
        try {
            $designImages = $this->designImageCollection->create()->addFieldToFilter('design_id', Array('eq' => $design_id));
            $designModel = $this->designFactory->create();
            $designTemplate = $this->designFactory->create()->load($design_id);

            foreach ($designImages as $image) {
                $this->designImageFactory->create()
                        ->load($image->getId())
                        ->delete();
            }
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $designImagePath = $reader->getAbsolutePath() . 'productdesigner';
            $designImagePath .= DIRECTORY_SEPARATOR . 'designs';
            $imagePath = $designImagePath . DIRECTORY_SEPARATOR . $designTemplate->getDesignId();
            $this->deleteAllImagesFromPath($imagePath);
            $designTemplate->delete();

            $designModel->setDesignId($design_id)->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteAllImagesFromPath($path) {
        foreach (glob("{$path}/*") as $file) {
            if (is_dir($file)) {
                $this->deleteAllImagesFromPath($file);
            }
            if (file_exists($file)) {
                unlink($file);
            }
        }
        if (file_exists($path)) {
            rmdir($path);
        }
    }

    public function fetchCustomerUploadImages($customer_id, $limit, $page) {
        $customerImagesArray = [];
        $page = $page;
     
        $customerCollection = $this->customerImagesCollectionFactory->create()
        ->addFieldToFilter('customer_id', Array('eq' => $customer_id));
        $totalRecords = $customerCollection->count();
        $customerCollection = $this->customerImagesCollectionFactory->create()
        ->setCurPage($page)
        ->setPageSize($limit)
        ->addFieldToFilter('customer_id', Array('eq' => $customer_id));
        // $customerCollection;
        // $customerCollection;
        $customerCollection->setOrder('id', 'DESC');
        $count = 0;
        $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/upload';
        $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $dirImg = $reader->getAbsolutePath() . 'productdesigner' . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR;
        foreach ($customerCollection->getData() as $customerimages) {
            if (file_exists($dirImg . $customerimages['image_path'])) {
                $customerImages['url'] = $path . $customerimages['image_path'];
                $customerImages['medium_url'] = $path . $customerimages['image_path'];
                $customerImages['id'] = $customerimages['id'];
                $customerImagesArray[] = $customerImages;
            }
        }
        $totalImages = count($customerImagesArray);
        if ($totalRecords <= ($limit * $page) || $totalImages <= 0) {
            $loadMoreFlag = 0;
        } else {
            $loadMoreFlag = 1;
        }
        $customerImagesData['imageData'] = $customerImagesArray;
        $customerImagesData['loadMore'] = $loadMoreFlag;
        return $customerImagesData;
    }

}
