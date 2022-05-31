<?php
/**
 * Copyright © Biztech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Magemobcart\Controller\Adminhtml\Magemobcart;

use Magento\Framework\Module\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Exportxml extends \Magento\Backend\App\Action
{
    protected $categoryModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Biztech\Magemobcart\Model\Magemobcart $categoryModel
    ) {
        $this->_responseInterface = $context->getResponse();
        $this->_categoryModel = $categoryModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryCollection = $this->_categoryModel->getCollection()->addFieldToSelect('title')->addFieldToSelect('magemobcart_id')->addFieldToSelect('filename')->addFieldToSelect('filepath')->addFieldToSelect('status')->addFieldToSelect('sort_order')->getData();
        $categoryArray['article'] = $categoryCollection;
        $xml = new \DOMDocument();

        $rootNode = $xml->appendChild($xml->createElement("items"));

        foreach ($categoryArray['article'] as $article) {
            if (! empty($article)) {
                $itemNode = $rootNode->appendChild($xml->createElement('item'));
                foreach ($article as $k => $v) {
                    $itemNode->appendChild($xml->createElement($k, $v));
                }
            }
        }

        $xml->formatOutput = true;

        $backup_file_name = 'category_block.xml';
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
