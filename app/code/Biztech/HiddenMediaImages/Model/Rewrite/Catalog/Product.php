<?php

namespace Biztech\HiddenMediaImages\Model\Rewrite\Catalog;
use Magento\Framework\App\Filesystem\DirectoryList;

class Product extends \Magento\Catalog\Model\Product {

 public function getAllMediaGalleryImages()
    {
            $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
          if (!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
              $images = $this->_collectionFactory->create();
              foreach ($this->getMediaGallery('images') as $image) {
                  $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                  $image['id'] = $image['value_id'];
                  $image['path'] = $directory->getAbsolutePath($this->getMediaConfig()->getMediaPath($image['file']));
                  $images->addItem(new \Magento\Framework\DataObject($image));
              }
              $this->setData('media_gallery_images', $images);
          }

          return $this->getData('media_gallery_images');
    }

 }