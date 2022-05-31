<?php
namespace Prince\Productattach\Controller\Index;

//use Magento\Framework\App\Action\Context;
//use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class ProductAttachment extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    protected $resultPageFactory;

	public function __construct(
    	\Magento\Framework\App\Action\Context $context, 
    	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

	public function execute()
    {
    	$requestedSku = $this->getRequest()->getParam("sku");
        $selectedValues = $this->getRequest()->getParam("selectedvalues");
        $resultarray['attachment'] = '';
        $htmlString = '';
        if($selectedValues){
            parse_str($selectedValues, $attributeOptions);
            if($attributeOptions){
                if(isset($attributeOptions['super_attribute']) && !empty($attributeOptions['super_attribute'])) {
                    $attributesInfo = $attributeOptions['super_attribute'];
                    $product = $this->_productRepository->get($requestedSku);
                    $simpleproduct = $product->getTypeInstance(true)->getProductByAttributes($attributesInfo, $product);
                    $resultPage = $this->_resultPageFactory->create();
                    $blockhtml = $resultPage->getLayout()->createBlock('Prince\Productattach\Block\Attachment')
                            ->setProductId($simpleproduct->getId())
                            ->setData('parent_product_id',$product->getId())
                            ->setTemplate('Prince_Productattach::attachment.phtml')->toHtml();
                    $resultarray['attachment'] = $blockhtml;
                    $resultarray['config_product_sku'] = $product->getSku();
                    $resultarray['simple_product_sku'] = $simpleproduct->getSku();
                }
            }
            $resultarray['status'] = 'success';
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($resultarray);
       	return $resultJson;
    }
}