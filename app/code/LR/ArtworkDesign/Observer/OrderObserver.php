<?php
namespace LR\ArtworkDesign\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
use LR\ArtworkDesign\Model\ArtworkDesignFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class OrderObserver implements ObserverInterface
{
	const XML_PATH_EMAIL_RECIPIENT = 'artwork_design/email/send_email_to';
    const XML_PATH_EMAIL_TEMPLATE = 'artwork_design/email/email_template';

    protected $_artworkdesign;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $storeManager;
    protected $_escaper;
    protected $jsonResultFactory;
    protected $serializer;
    protected $_checkoutSession;    

    public function __construct(
        ArtworkDesignFactory $artworkdesign,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        \LR\ArtworkDesign\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        SerializerInterface $serializer,
        CheckoutSession $checkoutSession
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
        $this->serializer = $serializer;
        $this->_checkoutSession = $checkoutSession;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
       //	$statuscode = $observer->getEvent()->getOrder()->getStatus();
       //	$statuslabel = $observer->getEvent()->getOrder()->getStatusLabel();

       	$post = $this->_checkoutSession->getEmailParams();
        if($post)
        {
           	$emailComments = $post['artworkdesign_comment'];
            $comments = array();
            $comments[] = array('is_customer'=>1,'comment'=>$post['artworkdesign_comment']);
            //$comments[]['comment'] = $post['artworkdesign_comment'];

            $post['artworkdesign_comment'] = $this->serializer->serialize($comments);
            $this->inlineTranslation->suspend();
            $post['status'] = 0;
            $artworkdesign = $this->_artworkdesign->create();
            $artworkdesign->setData($post);
            $artworkdesign->save();

            $post['artworkdesign_comment'] = $emailComments;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);
             
            $error = false;
             
            $sender = [
                'name' => $this->_escaper->escapeHtml($post['artworkdesign_name']),
                'email' => $this->_escaper->escapeHtml($post['artworkdesign_email']),
            ];
             
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $emails = explode(",",$this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope));

            $fileContent= '';
            if(isset($post['full_path']))
            {
                $fileContent = file_get_contents($post['full_path']);
            }
            
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
                    ->addTo($email, $storeScope);
                    if($fileContent != '' && isset($post['image_name']))
                    {
                        $transport->addAttachment($fileContent, $post['image_name'],'image');
                    }
                 
                $transport->getTransport()->sendMessage();
            }
            $this->inlineTranslation->resume();
            $post = $this->_checkoutSession->unsEmailParams();         	  
        } 
    }
}