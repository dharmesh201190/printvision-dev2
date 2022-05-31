<?php

namespace Biztech\Productdesigner\Plugin\Minicart;

class Image {

    protected $_bizHelper;
    protected $_storeManager;
    protected $designImageCollection;

    public function __construct(
    \Biztech\Productdesigner\Helper\Data $bizHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Model\Mysql4\Designimages\CollectionFactory $designImageCollection
    ) {
        $this->_bizHelper = $bizHelper;
        $this->_storeManager = $storeManager;
        $this->designImageCollection = $designImageCollection;
    }

    public function aroundGetItemData($subject, $proceed, $item) {
        $result = $proceed($item);
        $design_id = '';
        $params = $item->getProduct()->getCustomOptions();
        $notAllowToEdit = '';
        foreach ($params as $key => $pram) {
            if ($key == 'additional_options') {
                $designData = $pram->getData();
                $designdata1 = $this->_bizHelper->unserializeData($designData['value']);
                foreach ($designdata1 as $dData) {
                    if (isset($dData['code']))
                    if ($dData['code'] == 'product_design') {
                        $design_id = $dData['design_id'];
                    }
                    if (isset($dData['code']))
                        if ($dData['code'] == "printing_method" || $dData['code'] == "name_numbers") {
                        $notAllowToEdit = "true";
                    }
                }
            }
        }
        if ($design_id != '') {
            $designImages = $this->designImageCollection->create()->addFieldToFilter('design_id', array('eq' => $design_id))->addFieldToFilter('design_image_type', 'base')->getFirstItem()->getData();
            if (isset($designImages['image_path'])) {
                $path = $designImages['image_path'];
                $product = $item->getProduct();
                $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $url = $mediaUrl . "productdesigner/designs/" . $design_id . "/base/" . $path;

                $result['product_url'] = $this->_storeManager->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $product->getId() . '/design/' . base64_encode($design_id) . '/item/' . base64_encode($item->getId());
                $result['configure_url'] = $this->_storeManager->getStore()->getBaseUrl() . 'productdesigner/index/index/id/' . $product->getId() . '/design/' . base64_encode($design_id) . '/item/' . base64_encode($item->getId());
                $result['product_image']['src'] = $url;
                $result['design'] = $design_id;
                $result['notallowtoedit'] = $notAllowToEdit;
            } else {
                $result['design'] = '';
                $result['notallowtoedit'] = '';
            }
        } else {
            $result['design'] = '';
            $result['notallowtoedit'] = '';
        }
        return $result;
    }

}
