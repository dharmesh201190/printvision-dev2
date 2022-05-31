<?php

namespace Biztech\DPI\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class CustomSize implements ObserverInterface {

    protected $_filesystem;
    protected $_imageFactory;
    protected $_storeManager;
    protected $_productFactory;
    protected $_catalogProductTypeConfigurable;
    protected $infoHelper;

    public function __construct(
    \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable, \Biztech\Productdesigner\Helper\Info $infoHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->infoHelper = $infoHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $imagedata = $observer->getEvent()->getData('imagedata');
        $image = $imagedata->getImage();
        $productid = $image['product_id'];
        $product = $this->_catalogProductTypeConfigurable->getParentIdsByChild($productid);
        if (isset($product[0])) {
            $parentProductId = $product[0];
        } else {
            $parentProductId = $productid;
        }
        $dimentions = $this->getImageSize($productid, $parentProductId, $image['imagepath']);
        $url = $this->resize($image['imagepath'], $image['base_path'], $dimentions[0], $dimentions[1]);
        $updatedResource = $this->generateAndResize($image['imagepath'], $url, $image['source'], $image['allDimensions'], $image['base_path'], $dimentions[0], $dimentions[1]);
        $imagedata->setImage(array('imagepath' => $url, 'source' => $updatedResource));
    }

    public function resize($image, $dir, $width = null, $height = null) {
        if ($width == null || $height == null) {
            return $image;
        }
        $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dir) . basename($image);
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($image);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(false);
        $imageResize->resize($width, $height);
        $dest = $imageResized;
        $imageResize->save($dest);
        $resizedURL = $dir . basename($image);
        return $resizedURL;
    }

    public function getImageSize($productid, $parentProductId, $image) {
        list($iWidth, $iHeight) = getimagesize($image);
        $product = $this->_productFactory->create()->load($productid);
        $parentProduct = $this->_productFactory->create()->load($parentProductId);
        $customOutputWidth = $product->getOutputWidth() ?: $parentProduct->getOutputWidth();
        $customOutputHeight = $product->getOutputHeight() ?: $parentProduct->getOutputHeight();
        $width = $customOutputWidth ?: $iWidth;
        $height = $customOutputHeight ?: $iHeight;
        return array($width, $height);
    }

    public function generateAndResize($prod_image_path, $updated_prod_image_path, $images, $allDimensions, $base_path, $width = null, $height = null) {
        list($resize_width, $resize_height) = $this->infoHelper->calculateResizeWidthHeight($prod_image_path);
        list($iWidth, $iHeight) = getimagesize($updated_prod_image_path);
        $updatedResource = [];
        $allDimensionsSizes = [];
        foreach ($allDimensions as $dimension) {
            $selection_area = json_decode($dimension['selection_area'], true);
            $allDimensionsSizes[$dimension['design_area_id']] = array(
                'width' => $selection_area['width'],
                'height' => $selection_area['height']
            );
        }
        foreach ($images as $index => $image) {
            $path = $base_path . 'orig.png';
            imagesavealpha($image, true);
            imagepng($image, $path);
            imagedestroy($image);
            if (isset($allDimensionsSizes[$index])) {
                $dimensionWidth = $allDimensionsSizes[$index]['width'];
                $dimensionHeight = $allDimensionsSizes[$index]['height'];
            }
            if ($dimensionWidth && $dimensionHeight) {
                $width = ($iWidth * $dimensionWidth) / $resize_width;
                $height = ($iHeight * $dimensionHeight) / $resize_height;
            }
            $this->resize($path, $base_path, $width, $height);
            $updatedResource[$index] = imagecreatefrompng($path);
        }
        return $updatedResource;
    }

}
