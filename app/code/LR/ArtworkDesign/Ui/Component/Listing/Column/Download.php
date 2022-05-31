<?php

namespace LR\ArtworkDesign\Ui\Component\Listing\Column;

/**
 * Class Download
 * @package LR\ArtworkDesign\Ui\Component\Listing\Column
 */
class Download extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Download constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $url = $this->urlBuilder->getBaseUrl().'pub/media/'.$item['artworkdesign_image'];
                
                //$item[$this->getData('name')] = "<a href=".$url." download>Download</a>";

                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $url,
                        'label' => __('Download'),
                        'target' => '_blank'
                    ]
                ];
            }
        }
        
        return $dataSource;
    }
}
