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
namespace Webkul\JobBoard\Block\Adminhtml\Category;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class GenericButton
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var URLBuilder
     */
    protected $urlBuilder;
    
    /**
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }
    
    /**
     * Get Category Id from Registry
     *
     * @return Int|Null $categoryId
     */
    public function getId()
    {
        $jobboardCategory = $this->registry->registry('jobboard_category');
        return $jobboardCategory ? $jobboardCategory->getId() : null;
    }

    /**
     * Get Url
     *
     * @param string $route
     * @param array $params
     *
     * @return String
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
