<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Block\Adminhtml\Offerslider\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractRenderer
{
    protected $productModel;
    protected $storeManager;
    protected $imageHelper;

    /**
     * @param Context $context
     * @param Product $productModel
     * @param StoreManagerInterface $storeManager
     * @param CatalogImageHelper $imageHelper
     */
    public function __construct(
        Context $context,
        Product $productModel,
        StoreManagerInterface $storeManager,
        CatalogImageHelper $imageHelper
    ) {
        $this->productModel = $productModel;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productImage = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productImage = $productImage."Magemobcart/Offers/".$row->getData('filename');
        $width = 90;
        $height = 90;
        $imageUrl = "banner";
        return '<img class="admin__control-thumbnail" src="' . $productImage . '" width="' . $width . '" height="' . $height . '" alt="' . $imageUrl . '"/>';
     

        return parent::render($row);
    }
}
