<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Notification;

use Magento\Framework\Module\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Exportcsv extends \Magento\Backend\App\Action
{
    protected $moduleManager;
    protected $scopeConfig;
    protected $resultPageFactory;
    public $catalogConfig;
    protected $productFactory;
    protected $request;
    protected $responseInterface;
    protected $resourceConnection;
    protected $warehouseModel;
    protected $urlInterface;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Manager $moduleManager,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleManager = $moduleManager;
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->request = $request;
        $this->responseInterface = $context->getResponse();
        $this->resourceConnection = $resourceConnection;
        $this->urlInterface = $context->getBackendUrl();
        parent::__construct($context);
    }

    /**
     * This function is used for export all products with warehouse qty details
     * @return Void
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collections = $objectManager->get('Biztech\Magemobcart\Model\Notification')->getCollection();
        $response = [
            ['ID','Title','Send']
        ];
        foreach ($collections as $data) {
            $notificationId = $data->getNotificationId();
            $title = $data->getTitle();
            $isSent = $data->getIsSent();
            if ($isSent == 1) {
                $isSent = "Yes";
            } else {
                $isSent = "No";
            }
            $response[] = [$notificationId,$title,$isSent];
        }
        $content = '';
        $fileName = 'notification.csv';
        foreach ($response as $line) {
            $content .= '"' . implode('","', $line) . '",' . "\n";
        }
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * This function is used for prepare the sheet of the products
     * @param  String $fileName
     * @param  String $content
     * @param  string $contentType
     * @return void
     */
    public function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->responseInterface;
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
