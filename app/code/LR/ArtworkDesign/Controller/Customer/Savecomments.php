<?php 
namespace LR\ArtworkDesign\Controller\Customer;
use Magento\Framework\Serialize\SerializerInterface;  
class Savecomments extends \Magento\Framework\App\Action\Action 
{ 
	const XML_PATH_EMAIL_RECIPIENT = 'artwork_design/email/send_email_to';
    const XML_PATH_EMAIL_TEMPLATE = 'artwork_design/email/email_template';

	protected $_artworkdesign;
	protected $_serializer;

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

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \LR\ArtworkDesign\Model\ArtworkDesignFactory $artworkdesign,
        \LR\ArtworkDesign\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        SerializerInterface $serializer
    ) {
        $this->_artworkdesign = $artworkdesign;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_serializer = $serializer;
        parent::__construct($context);
    }

	public function execute() { 
		$post = $this->getRequest()->getParams();
		$id = $post['id'];
		$customerComment = $post['comments'];
		$artWork = $this->_artworkdesign->create()->load($id);
		$artWorkComments = $this->_serializer->unserialize($artWork->getArtworkdesignComment());
		$artWorkComments[] = array('is_customer'=>1,'comment'=>$customerComment);
		
		$customerName = $artWork->getArtworkdesignName();
        $customerEmail = $artWork->getArtworkdesignEmail();
        $customerPhone = $artWork->getArtworkdesignPhone();

		$this->inlineTranslation->suspend();
        try {
            $artWork->setArtworkdesignComment($this->_serializer->serialize($artWorkComments))->save();

            $data = array();
            $data['artworkdesign_name'] = $customerName;
            $data['artworkdesign_email'] = $customerEmail;
            $data['artworkdesign_phone'] = $customerPhone;
            $data['artworkdesign_comment'] = $customerComment;

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
             
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
                    ->addTo($email, $storeScope)
                    ->getTransport();
                 
                $transport->sendMessage();
            }
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccessMessage(__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.'));
            $this->_redirect('artworkdesign/customer/index');

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(__("We can\'t process your request right now. Sorry, that\'s all we know.".$e->getMessage()));
            $this->_redirect('artworkdesign/customer/index');
        }
	} 
} 
