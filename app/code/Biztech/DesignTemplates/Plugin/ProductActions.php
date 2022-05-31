<?php
namespace Biztech\DesignTemplates\Plugin;
class ProductActions
{
    protected $context;
    protected $urlBuilder;
    protected $_info;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Biztech\Productdesigner\Helper\Info $info
    )
    {
        $this->context = $context;
        $this->urlBuilder = $urlBuilder;        
        $this->_info = $info;
    }

    /*
    **This Plugin for add MakeDesign Action in Catalog Action colum.
    */

    public function afterPrepareDataSource(
        \Magento\Catalog\Ui\Component\Listing\Columns\ProductActions $subject,
        array $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                $id = $item['entity_id'];
                if ($this->_info->isPdEnable($id)) {
                    $item[$subject->getData('name')]['do_something'] = [
                        'href' => $this->urlBuilder->getUrl(
                                'designtemplates/Productdesigner/Index', ['id' => $item['entity_id']]
                        ),
                        'label' => __('Make Design'),
                        'hidden' => false,
                    ];
                }
            }
        }
        return $dataSource;
    }
}