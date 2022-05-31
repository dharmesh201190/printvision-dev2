<?php

/**
 * Copyright © 2017-2018 AppJetty. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Biztech\Productdesigner\Controller\Index;

class generateSvg extends \Magento\Framework\App\Action\Action {

    // Variable Declaration
    protected $_storeManager;
    protected $infoHelper;
    protected $design;
    protected $dir;
    protected $designImagesFactory;

    // for flag
    protected $designOrderCollection;

    /**
     * Dependency Injector
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Biztech\Productdesigner\Helper\Info $infoHelper, \Biztech\Productdesigner\Model\DesignsFactory $design, \Magento\Framework\Filesystem\DirectoryList $dir, \Biztech\Productdesigner\Model\DesignimagesFactory $designImagesFactory, \Biztech\Productdesigner\Model\Mysql4\DesignOrders\CollectionFactory $designOrderCollection
    ) {
        $this->_storeManager = $storeManager;
        $this->infoHelper = $infoHelper;
        $this->design = $design;
        $this->dir = $dir;
        $this->designImagesFactory = $designImagesFactory;
        $this->designOrderCollection = $designOrderCollection;
        parent::__construct($context);
    }

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);

            $designId = $data['designId'];
            $imageId = $data['imageId'];
            $canvasSVG = isset($data['canvasSVG']) ? json_decode(base64_decode($data['canvasSVG'])) : '';
            $svgImagesNames = $this->generateSVGImages($canvasSVG, $designId, $imageId);
            $this->saveGenerateImages($svgImagesNames);
            $this->getResponse()->setBody(json_encode(array("status" => "success")));
        } catch (\Exception $e) {
            $response = $this->infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    private function generateSVGImages($canvasSVG, $designId, $imageId) {
        $mediaPath = $this->dir->getPath('media');
        $svgDir = $mediaPath . '/productdesigner/designs/' . $designId . '/svg/';
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $index = 0;
        $svg_name = "/svg_" . $index++ . "_" . $time . ".svg";
        $svgImagesNames = array('name' => $svg_name, 'designId' => $designId, 'image_id' => $imageId);
        preg_match_all('/<image[^>]+>/i', $canvasSVG, $images);
        foreach ($images[0] as $image) {
            preg_match_all('~<image(.*?)xlink:href="([^"]+)"(.*?)>~', $image, $matches);
            foreach ($matches as $attrs) {
                $url = parse_url($attrs[0]);
                if (!empty($url['scheme']) && ($url['scheme'] == 'http' || $url['scheme'] == 'https')) {
                    $ext = pathinfo($attrs[0], PATHINFO_EXTENSION);
                    $filePath = $this->infoHelper->convertRelToAbsPath($attrs[0]);
                    $img = file_get_contents($filePath);
                    $base64Data = "";
                    if($ext == 'jpg' || $ext == 'JPG'){
                        $ext = 'jpeg';
                    }
                    if ($img !== false) {
                        $base64Data = 'data:image/' . $ext . ';base64,' . base64_encode($img);
                    }
                    $secureImg = str_replace($attrs[0], $base64Data, $image);
                    $canvasSVG = str_replace($image, $secureImg, $canvasSVG);
                }
            }
        }
        try {
            if (!file_exists($svgDir) && !empty($designId)) {
                mkdir($svgDir, 0777, true);
            }
            if(!empty($designId)){
                $fp = fopen($svgDir . $svg_name, 'w');
                fwrite($fp, $canvasSVG);
                fclose($fp);
            }
        } catch (\Exception $e) {
            $response = $this->infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }

        return $svgImagesNames;
    }

    private function saveGenerateImages($svgImagesNames) {
        $designImagesCollection = $this->designImagesFactory->create()->getCollection()
        ->addFieldToFilter('design_id', $svgImagesNames['designId'])
        ->addFieldToFilter('product_image_id', $svgImagesNames['image_id'])
        ->addFieldToFilter('design_image_type', array('in' => array('svg')));
        $designImagesCollection->walk('delete');

        $designImagesModel = $this->designImagesFactory->create();
        $designImagesModel->setDesignId($svgImagesNames['designId'])
        ->setDesignImageType('svg')
        ->setProductImageId($svgImagesNames['image_id'])
        ->setImagePath(str_replace('\\', '/', $svgImagesNames['name']));
        $designImagesModel->save();

        // set flag value
        $designOrderCollectionModel = $this->designOrderCollection->create()->addFieldToFilter('design_id', ['eq' => $svgImagesNames['designId']]);
        foreach ($designOrderCollectionModel as $value) {
            if ($value->getFlag() == 1) {
                $value->setFlag(0);
            }
        }
        $designOrderCollectionModel->save();
    }

}
