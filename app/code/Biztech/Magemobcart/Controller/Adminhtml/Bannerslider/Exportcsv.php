<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Bannerslider;

use Magento\Framework\Module\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Exportcsv extends \Magento\Backend\App\Action
{
    protected $bannersliderModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Biztech\Magemobcart\Model\Bannerslider $bannersliderModel
    ) {
        $this->_responseInterface = $context->getResponse();
        $this->_bannersliderModel = $bannersliderModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $bannerSliderCollections = $this->_bannersliderModel->getCollection();
        $response = [
            ['ID','Title','Banner','Thumbnail','Status','Sort order']
        ];
        foreach ($bannerSliderCollections as $data) {
            $bannersliderId = $data->getBannersliderId();
            $title = $data->getTitle();
            $filename = $data->getFilename();
            $filePath = $data->getFilepath();
            $sortOrder = $data->getSortOrder();
            $status = $data->getStatus();
            if ($status == 1) {
                $status = "Enabled";
            } else {
                $status = "Disabled";
            }
            $response[] = [$bannersliderId,$title,$filePath,$filename,$status,$sortOrder];
        }
        $content = '';
        $fileName = 'bannerslider.csv';
        foreach ($response as $line) {
            $content .= '"' . implode('","', $line) . '",' . "\n";
        }
        $this->_sendUploadResponse($fileName, $content);
    }
    public function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->_responseInterface;
        $response->setHttpResponseCode(200);
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        return;
    }
}
