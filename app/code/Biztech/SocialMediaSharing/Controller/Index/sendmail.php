<?php

namespace Biztech\SocialMediaSharing\Controller\Index;

class sendmail extends \Magento\Framework\App\Action\Action {

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'section/group/your_email_template_field_id';

    protected $_scopeConfig;

    protected $_storeManager;

    protected $inlineTranslation;

    protected $_transportBuilder;

    protected $temp_id;
    protected $_logo;
    protected $_designCollection;
    protected $_config;
    protected $stateInterface;
  
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Theme\Block\Html\Header\Logo $logo, \Biztech\Productdesigner\Model\Mysql4\Designimages\Collection $designCollection, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Framework\Translate\Inline\StateInterface $stateInterface
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_logo = $logo;
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_designCollection = $designCollection;
        $this->_config = $config;
        $this->stateInterface = $stateInterface;
    }

    public function execute() {
        $images = array();
        $data = $data = json_decode(file_get_contents('php://input'), TRUE);
        $receipentMail = $data['receipentMail'];
        $design_id = $data['design_id'];
        $product_id = $data['product_id'];

        // get design

        $obj_product = $this->_designCollection->addFieldToFilter('design_id', Array('eq' => $design_id))->addFieldToFilter('design_image_type', 'base');
        $designImages = $obj_product->getData();
        
        foreach ($designImages as $key => $designImage) {
            array_push($images, $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productdesigner/designs/' . $design_id . '/base/' . $designImage['image_path']);
        }
        $mediUrl = $this->_url->getUrl('productdesigner/index/index') . "id/" . $product_id . "/design/" . base64_encode($design_id);

        // send mail        
        $toEmail = $this->_config->getValue('trans_email/ident_sales/email');
        $toName = $this->_config->getValue('trans_email/ident_sales/name');
        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
        
        $templateVars = array(
            'store' => $this->_storeManager->getStore(),
            'images' => $images,
            'logo_url' => $this->_logo->getLogoSrc(),
            'logo_alt' => $this->_logo->getLogoAlt(),
            'mediUrl' => $mediUrl
        );
        $from = array('email' => $toEmail, 'name' => $toName);
        $this->stateInterface->suspend();
        $to = array($receipentMail);
        $transport = $this->_transportBuilder->setTemplateIdentifier('biztech_mail_share')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
        $result = array();
        $result['status'] = 'success';
        $this->getResponse()->setBody(json_encode($result));
    }
}
