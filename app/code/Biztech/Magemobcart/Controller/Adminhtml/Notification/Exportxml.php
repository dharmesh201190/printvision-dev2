<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Notification;

use Magento\Framework\Module\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Exportxml extends \Magento\Backend\App\Action
{
    protected $notificationModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Biztech\Magemobcart\Model\Notification $notificationModel
    ) {
        $this->_responseInterface = $context->getResponse();
        $this->_notificationModel = $notificationModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $notificationCollection = $this->_notificationModel->getCollection()->addFieldToSelect('notification_id')->addFieldToSelect('title')->addFieldToSelect('is_sent')->getData();
        $notificationArray['article'] = $notificationCollection;
        $xml = new \DOMDocument();

        $rootNode = $xml->appendChild($xml->createElement("items"));

        foreach ($notificationArray['article'] as $article) {
            if (! empty($article)) {
                $itemNode = $rootNode->appendChild($xml->createElement('item'));
                foreach ($article as $k => $v) {
                    $itemNode->appendChild($xml->createElement($k, $v));
                }
            }
        }

        $xml->formatOutput = true;

        $backup_file_name = 'notification.xml';
        $xml->save($backup_file_name);

        header('Content-Description: File Transfer');
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file_name));
        // ob_clean();
        // flush();
        readfile($backup_file_name);
        // exec('rm ' . $backup_file_name);
    }
}
