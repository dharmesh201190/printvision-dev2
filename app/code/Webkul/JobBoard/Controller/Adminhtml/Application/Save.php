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
use Webkul\JobBoard\Model\ApplicationFactory;
use Webkul\JobBoard\Model\ResumeUploaderFactory;
use Webkul\JobBoard\Model\JobFactory;
use Webkul\JobBoard\Helper\Data;

class Save extends Action
{
    /**
     * @var \Webkul\JobBoard\Model\ApplicationFactory
     */
    protected $applicationFactory;

    /**
     * @var \Webkul\JobBoard\Model\ResumeUploaderFactory
     */
    protected $resumeUploaderFactory;

    /**
     * @var \Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @var \Webkul\JobBoard\Helper\Data
     */
    protected $helper;

    /**
     * @param Context               $context
     * @param ApplicationFactory    $applicationFactory
     * @param ResumeUploaderFactory $resumeUploaderFactory
     * @param JobFactory            $jobFactory
     * @param Data                  $helper
     */
    public function __construct(
        Context $context,
        ApplicationFactory $applicationFactory,
        ResumeUploaderFactory $resumeUploaderFactory,
        JobFactory $jobFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->applicationFactory = $applicationFactory;
        $this->resumeUploaderFactory = $resumeUploaderFactory;
        $this->jobFactory = $jobFactory;
        $this->helper = $helper;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->isPost()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $applicationRecord = $this->getRequest()->getParams();
            if (isset($applicationRecord['resume'][0]['name'])) {
                $applicationRecord['resume'] = $applicationRecord['resume'][0]['name'];
            }
            
            $applicationModel = $this->applicationFactory->create();
            $applicantId = $this->getRequest()->getParam('entity_id');
            if ($this->getRequest()->getParam('entity_id')) {
                $applicationModel->load($this->getRequest()->getParam('entity_id'));
            }
            
            try {
                $this->resumeUploaderFactory->create()->moveFileFromTmp($applicationRecord['resume']);
            } catch (\Exception $e) {
                if ($applicantId) {
                    $applicationRecord['resume'] = $applicationModel['resume'];
                } else {
                    $this->messageManager->addError(__("Something went wrong"));
                    return $resultRedirect->setPath('*/*/');
                }
            }
   
            $applicationModel->setData($applicationRecord);
            $applicationModel->save();
            $this->sendApplicationNotification($applicationRecord);
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        $this->messageManager->addSuccess(__("Job saved successfully"));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Send Notification Mail to Applicant & Admin
     *
     * @param Array $data
     */
    protected function sendApplicationNotification($data)
    {
        $applicantName = $data['firstname']." ".$data['lastname'];
        $jobPosition = $this->jobFactory->create()->load($data['job'])->getDesignation();

        $applicantInfo = [
            'name' => $applicantName,
            'email' => $data['email'],
            'resume' => $data['resume']
        ];
        $adminInfo = [
            'name' => $this->helper->getAdminName(),
            'email' => $this->helper->getAdminMail(),
            'resume' => $data['resume']
        ];
        
        $applicantTemplateVariables = [
            'applicant_name' => $applicantName,
            'job_position' => $jobPosition
        ];

        $adminTemplateVariables = [
            'admin_name' => $this->helper->getAdminName(),
            'applicant_name' => $applicantName,
            'job_position' => $jobPosition,
            'qualification' => $data['qualification'],
            'experience' => $data['experience'],
            'telephone' => $data['telephone']
        ];

        try {
            $applicantTemplate = Data::XML_PATH_REPLY_MAIL_TO_APPLICANT_BY_ADMIN;
            $this->helper->customMailSendMethod(
                $applicantTemplateVariables,
                $adminInfo,
                $applicantInfo,
                $applicantTemplate
            );

            $adminTemplate = Data::XML_PATH_REPLY_MAIL_TO_ADMIN_BY_ADMIN;
            $this->helper->customMailSendMethod(
                $adminTemplateVariables,
                $applicantInfo,
                $adminInfo,
                $adminTemplate
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
