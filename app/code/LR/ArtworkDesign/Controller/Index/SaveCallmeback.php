<?php

namespace LR\ArtworkDesign\Controller\Index;

use Magento\Framework\App\Action\Context;
use LR\ArtworkDesign\Model\ArtworkDesignFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;

class SaveCallmeback extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'artwork_design/email/send_email_to';
    const XML_PATH_EMAIL_TEMPLATE = 'artwork_design/email/callmeback_email_template';
    /**
     * @var ArtworkDesign
     */
    protected $_artworkdesign;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    /**
    * @var \Magento\Framework\Mail\Template\TransportBuilder
    */
    protected $_transportBuilder;
     
    /**
    * @var \Magento\Framework\Translate\Inline\StateInterface
    */
    protected $inlineTranslation;
     
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
     
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;
    /**
    * @var \Magento\Framework\Escaper
    */
    protected $_escaper;

    protected $jsonResultFactory;

    public function __construct(
        Context $context,
        ArtworkDesignFactory $artworkdesign,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        \LR\ArtworkDesign\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        $this->_artworkdesign = $artworkdesign;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $post = $this->getRequest()->getParams();
        $customerName = $post['callmeback_name'];
        $customerEmail = $post['callmeback_email'];
        $customerPhone = $post['callmeback_phone'];
        $customerComment = $post['callmeback_likecall'];

        $this->inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);
             
            $error = false;
             
            $sender = [
                'name' => $this->_escaper->escapeHtml($customerName),
                'email' => $this->_escaper->escapeHtml($customerEmail),
            ];
             
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $emails = explode(",",$this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope));
            foreach ($emails as $email)
            {
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope)) // this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ])
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    //->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->addTo($email, $storeScope)
                    ->getTransport();
                 
                $transport->sendMessage();
            }
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccessMessage(__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.'));
            $response = array('success'=>1);

            return $this->jsonResultFactory->create()->setData($response);

        } catch (\Exception $e) {

            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(__("We can\'t process your request right now. Sorry, that\'s all we know.".$e->getMessage()));
            $response = array('success'=>0);
            return $this->jsonResultFactory->create()->setData($response);

        }
    }
}
