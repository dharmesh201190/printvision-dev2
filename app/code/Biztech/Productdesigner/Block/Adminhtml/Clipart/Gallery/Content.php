<?php

namespace Biztech\Productdesigner\Block\Adminhtml\Clipart\Gallery;

class Content extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
\Magento\Framework\Data\Form\Element\Renderer\RendererInterface {

    protected $_template = 'Biztech_Productdesigner::productdesigner/clipart/gallery/content.phtml';
    protected $_mediaConfig;
    protected $_jsonEncoder;
    protected $clipartMediaCollection;
    protected $_storeManager;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Biztech\Productdesigner\Model\Mysql4\Clipartmedia\CollectionFactory $clipartMediaCollection, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product\Media\Config $mediaConfig, array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        $this->clipartMediaCollection = $clipartMediaCollection;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $this->_element = $element;
        $html = $this->toHtml();
        return $html;
    }

    public function getImagesJson($id, $page) {
        $images = $this->clipartMediaCollection->create()->addFieldToFilter("clipart_id", array("eq" => $id))->setPageSize(50)->setCurPage($page)->setOrder('position', 'ASC');
        if (count($images)) {
            foreach ($images as $image) {
                $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/clipart' . $image['image_path'];
                $image['url'] = $url;
                $image['file'] = $image['image_path'];
                $image['image_id'] = $image['image_id'];
                $image['label'] = $image['label'];
                $image['tags'] = $image['tags'];
                $image['position'] = $image['position'];
                $image['disabled'] = $image['disabled'];
            }
            $val1 = $this->_jsonEncoder->encode($images);
            $val2 = strstr($val1, '[', false);
            $val3 = rtrim($val2, "}");

            return $val3;
        }
        return '[]';
    }

    public function getLastPageNumber($id) {
        $images = $this->clipartMediaCollection->create()->addFieldToFilter("clipart_id", array("eq" => $id))->setPageSize(50);
        return $images->getLastPageNumber();
    }

}
