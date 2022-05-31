<?php
namespace Biztech\DesignTemplates\Ui\Component\Listing\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TemplateImage extends Column
{
    const ALT_FIELD = 'image';

    protected $storeManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])) {
            foreach($dataSource['data']['items'] as &$item) {
                $fieldName = $item['image'];                                
                $url = '';
                if($item['image'] != '') {
                    $url = $this->storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ).'productdesigner/templates/'.$item['designtemplates_id'].'/base/'.$item['image'];
                }

                $item['image_src'] = $url;
                $item['image_alt'] = !empty($item['image']) ? basename($item['image']) : self::ALT_FIELD ;
                $item['image_link'] = $this->urlBuilder->getUrl(
                    'designtemplates/productdesigner/index/id/'.$item['product_id'],
                    ['templates' => $item['designtemplates_id']]
                );
                $item['image_orig_src'] = $url;
            }
        }
        return $dataSource;
    }
}