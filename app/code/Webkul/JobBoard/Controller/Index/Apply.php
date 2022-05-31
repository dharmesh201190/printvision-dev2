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
namespace Webkul\JobBoard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Webkul\JobBoard\Model\JobFactory;
use Webkul\JobBoard\Model\ApplicationFactory;
use Webkul\JobBoard\Helper\Data;

class Apply extends Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Webkul\JobBoard\Model\JobFactory $jobFactory
     */
    protected $jobFactory;

    /**
     * @param \Webkul\JobBoard\Model\ApplicationFactory $applicationFactory
     */
    protected $applicationFactory;

    /**
     * @param \Webkul\JobBoard\Helper\Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param ManagerInterface $messageManager
     * @param Filesystem $fileSystem
     * @param UploaderFactory $fileUploaderFactory
     * @param JobFactory $jobFactory
     * @param ApplicationFactory $applicationFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        ManagerInterface $messageManager,
        Filesystem $fileSystem,
        UploaderFactory $fileUploaderFactory,
        JobFactory $jobFactory,
        ApplicationFactory $applicationFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $messageManager;
        $this->fileSystem = $fileSystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->jobFactory = $jobFactory;
        $this->applicationFactory = $applicationFactory;
        $this->helper = $helper;
    }

    /**
     * Apply Execute Function
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost()) {
            if ($this->formKeyValidator->validate($this->getRequest())) {
                try {
                    $data = $this->getRequest()->getParams();
                    $resume = $this->getRequest()->getFiles("resume");
                    $job = $this->getRequest()->getParam("job");
                    $jobCollection = $this->jobFactory->create()->getCollection()
                                     ->addFieldToFilter("entity_id", $job)
                                     ->addFieldToFilter("status", 1);

                    if (!$jobCollection->getSize()) {
                        $this->messageManager->addError(__("Job not exists."));
                        return $resultRedirect->setPath('*/*/index');
                    } else {
                        $application = $this->applicationFactory->create();
                        $resumePath = $this->uploadResume($resume);
                        if ($resumePath['error']) {
                            $this->messageManager->addError($resumePath['message']);
                            return $resultRedirect->setPath('*/*/index');
                        }
                        $data['resume'] = $resumePath['path'];
                        $application->setData($data);
                        $application->save();

                        $this->sendApplicationNotification($data);
                        
                        $this->messageManager->addSuccess(__("Application applied successfully."));
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Upload Resume to File Location
     *
     * @param Array $resume
     *
     * @return Array $result
     */
    protected function uploadResume($resume)
    {
        $result = [];
        $result['error'] = false;
        if (!isset($resume['size']) || $resume['error']) {
            $result['error'] = true;
            $result['message'] = __("Resume file not found. Please try again");
            return $result;
        }
        try {
            $allowedExtensions = $this->helper->getAllowedResumeExtensions();
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $path = $mediaDirectory->getAbsolutePath("jobboard/application");
            $uploader = $this->fileUploaderFactory->create(['fileId' => 'resume']);
            $uploader->setAllowedExtensions($allowedExtensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $resumeFile = $uploader->save($path);
            $result['path'] = $resumeFile['file'];
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = __("Error occurred during resume upload. Please try again");
        }
        return $result;
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
            'resume'=> $data['resume']
        ]; 
        $adminInfo = [
            'name' => $this->helper->getAdminName(),
            'email' => $this->helper->getAdminMail(),
            'resume'=> $data['resume']

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
            $applicantTemplate = Data::XML_PATH_REPLY_MAIL_TO_APPLICANT;
            $this->helper->customMailSendMethod(
                $applicantTemplateVariables,
                $adminInfo,
                $applicantInfo,
                $applicantTemplate
            );

            $adminTemplate = Data::XML_PATH_REPLY_MAIL_TO_ADMIN;
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
