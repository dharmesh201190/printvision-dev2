<?php
namespace Biztech\Productdesigner\Block\Adminhtml\Printablecolor;
 
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Registry;
 
class Color extends AbstractRenderer
{
    protected $registry;
    
    protected $attributeFactory;
    
    public function __construct(
        Registry $registry,
        AttributeFactory $attributeFactory,
        Context $context,
        array $data = array()
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
 
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        // Get default value:
        $color_code = parent::_getValue($row);        
        return '<span class="font_colors" style="display: block;text-align:center;height: 18px;width: 150px;margin:0 auto;background: '. $color_code.'"></span>';        
    }
}    
