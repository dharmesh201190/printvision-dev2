<?php

namespace Biztech\Productdesigner\Model\Plugin\Catalog\Product\Gallery;

class CreateHandler {

    protected $_productGallery;
    protected $_productMetadataInterface;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $productGallery,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface
    ) {
        $this->_productGallery = $productGallery;
        $this->_productMetadataInterface = $productMetadataInterface;
    }

    public function afterExecute(
        \Magento\Catalog\Model\Product\Gallery\CreateHandler $mediaGalleryCreateHandler, \Magento\Catalog\Model\Product $product
    ) {
        $value = $product->getData('media_gallery');        
        if (!empty($value)) {
            $this->processNewAndExistingImages($product, $value);
        }        
        return $product;
    }

    protected function processNewAndExistingImages($product, array &$images) {
       $galleryvalue = $this->_productGallery;
       $magentoEdition = $this->_productMetadataInterface->getEdition();
       $key = ($magentoEdition == 'Enterprise') ? 'row_id' : 'entity_id';
       foreach ($images['images'] as &$image) {            
        if (empty($image['removed'])) {
            $data = [];
            $galleryvalue->deleteGalleryValueInStore(
                $image['value_id'], $product->getData($key), $product->getStoreId()
            );
            $data['value_id'] = $image['value_id'];
            $data['label'] = isset($image['label']) ? $image['label'] : '';
            $data['position'] = isset($image['position']) ? (int) $image['position'] : 0;
            $data['disabled'] = isset($image['disabled']) ? (int) $image['disabled'] : 0;
            $data['store_id'] = (int) $product->getStoreId();
            $data[$key] = $product->getData($key);
            if (isset($image['image_side'])) {                    
                $data['image_side'] = $image['image_side'];                    
            } else if(isset($image['image_side_default']))
            {
                $data['image_side'] = $image['image_side_default'];                   
            }
            $galleryvalue->insertGalleryValueInStore($data);
        }
    }
}

}
