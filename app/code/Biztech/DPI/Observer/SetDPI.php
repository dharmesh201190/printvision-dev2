<?php

namespace Biztech\DPI\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetDPI implements ObserverInterface {

	protected $_scopeConfig;
	protected $dir;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Filesystem\DirectoryList $dir
	) {
		$this->_scopeConfig = $scopeConfig;
		$this->dir = $dir;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {

		// fetch event data
		$eventData = $observer->getEvent()->getData('eventData');
		
		// set dpi value
		// if dpi value is configured, fetch, set default otherwise
		$dpiValue = ($this->_scopeConfig->getValue('productdesigner/dpi_configuration/set_dpi')) ? $this->_scopeConfig->getValue('productdesigner/dpi_configuration/set_dpi') : $eventData['defaultDpiValue'];

		// get design path using design id
		$designPath = $this->dir->getPath('media') . '/productdesigner/designs/' . $eventData['designId'];

		// get design images
		$designedImages = $eventData['designedImages'];

		// traverse throgh designed images one by one		
		foreach ($designedImages as $imagesArr) {

			// each designimages will return array of images(base high and orig high)
			// so we will traverse another array
			foreach ($imagesArr as $key => $value) {

				// set path value to design path(till pub/media/productdesigner/designs)
				$path = $designPath;

				// determine image type and append path accordingly
				$path = ($key == 'base_high') ? $path . "/base/" : $path . "/orig/";
				
				// we may find image in png
				// so we will fetch image name, append jpg as extension and move ahead
				$jpgImageName = pathinfo($value);
				$jpgImageName = $jpgImageName['filename'] . ".jpg";

				// finally append image name with above specified path
				$path .= $jpgImageName;

				// get image contents
				$image = file_get_contents($path);

				// set dpi value
				$image = substr_replace($image, pack("cnn", 1, $dpiValue, $dpiValue), 13, 5);

				// replace new image with current image
				file_put_contents($path, $image);
			}
		}
	}
}
