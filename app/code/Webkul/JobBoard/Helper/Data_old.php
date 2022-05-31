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
namespace Webkul\JobBoard\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
#use Magento\Framework\Mail\Template\TransportBuilder;
use Webkul\JobBoard\Model\Mail\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class Data extends AbstractHelper
{
    /**
     * @param String ApplicantNotificationTemplate
     */
    const XML_PATH_REPLY_MAIL_TO_APPLICANT = 'jobboard/email_templates/notifyEmailToApplicant';
    
    /**
     * @param String AdminNotificationTemplate
     */
    const XML_PATH_REPLY_MAIL_TO_ADMIN = 'jobboard/email_templates/notifyEmailToAdmin';

    /**
     * @param String ApplicantNotificationTemplateCreatedByAdmin
     */
    const XML_PATH_REPLY_MAIL_TO_APPLICANT_BY_ADMIN = 'jobboard/email_templates/notifyEmailToApplicantAppliedByAdmin';

    /**
     * @param String ApplicantNotificationTemplateCreatedByAdmin
     */
    const XML_PATH_REPLY_MAIL_TO_ADMIN_BY_ADMIN = 'jobboard/email_templates/notifyEmailToAdminAppliedByAdmin';

    /**
     * @param \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Webkul\JobBoard\Model\Mail\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @param \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder      $transportBuilder
     * @param StateInterface        $inlineTranslation
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Get Admin Name for Email.
     *
     * @return String
     */
    public function getAdminName()
    {
        return $this->scopeConfig->getValue(
            'jobboard/general_settings/adminname',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Admin Email Address for Email.
     *
     * @return String
     */
    public function getAdminMail()
    {
        return $this->scopeConfig->getValue(
            'jobboard/general_settings/adminemail',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Heading for JobBoard Index Page.
     *
     * @return String
     */
    public function getJobBoardHeading()
    {
        return $this->scopeConfig->getValue(
            'jobboard/general_settings/jobboardlabel',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get JobBoard Content for JobBoard Index Page.
     *
     * @return String
     */
    public function getJobBoardContent()
    {
        return $this->scopeConfig->getValue(
            'jobboard/general_settings/jobboardcontent',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Allowed Extension as String from Config Settings
     *
     * @return String
     */
    public function getAllowedResumeExtension()
    {
        return $this->scopeConfig->getValue(
            'jobboard/general_settings/allowedresumeextensions',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Allowed Extension in Array Format from Config Settings
     *
     * @return Array
     */
    public function getAllowedResumeExtensions()
    {
        $allowedExtensions = explode(",", $this->getAllowedResumeExtension());
        return $allowedExtensions;
    }

    /**
     * Get values for config settings.
     *
     * @param String $path
     * @param Int    $storeId
     *
     * @return String
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    
    /**
     * Get Template value from Config Settings
     *
     * @param String $xmlPath
     *
     * @return String
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->storeManager->getStore()->getStoreId());
    }

    /**
     * Generate Email Template
     *
     * @param Array $emailTemplateVariables
     * @param Array $senderInfo
     * @param Array $receiverInfo
     *
     * @return Object $emailTemplate
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {       
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        // $rootPath  =  $directory->getPath('media');           
        // $attachmentFile = $rootPath."/jobboard/application/".$receiverInfo['resume']; 

        // $mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
        //     ->getAbsolutePath();
        // $pdf_file = $mediaPath.$receiverInfo['resume'];

        $template =  $this->transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions(
                [
                              'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                              'store' => $this->storeManager->getStore()->getId(),
                              ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
           // ->addAttachment(file_get_contents($attachmentFile));
             
        return $this;
    }

    /**
     * Send Email
     *
     * @param Array  $emailTemplateVariables
     * @param Array  $senderInfo
     * @param Array  $receiverInfo
     * @param Object $template
     */
    public function customMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo, $template)
    { 
        $this->temp_id = $this->getTemplateId($template);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * Get Media Url
     *
     * @return String
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    /**
     * Save Object
     *
     * @param Object $object
     */
    public function saveObject($object)
    {
        $object->save();
    }

    /**
     * Delete Object
     *
     * @param Object $object
     */
    public function deleteObject($object)
    {
        $object->delete();
    }
}
