<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\JobBoard\Controller\Adminhtml\Application;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Webkul\JobBoard\Model\ResumeUploader;

class UploadResume extends Action
{
    /**
     * @var \Webkul\JobBoard\Model\ResumeUploader
     */
    protected $resumeUploader;

    /**
     * @param Context $context
     * @param ResumeUploader $resumeUploader
     */
    public function __construct(
        Context $context,
        ResumeUploader $resumeUploader
    ) {
        parent::__construct($context);
        $this->resumeUploader = $resumeUploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $files = $this->getRequest()->getFiles();
            $result = $this->resumeUploader->saveFileToTmpDir($files['resume']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
