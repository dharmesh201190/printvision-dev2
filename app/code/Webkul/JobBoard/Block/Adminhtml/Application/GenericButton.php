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
namespace Webkul\JobBoard\Block\Adminhtml\Application;

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
     * Get Application Id from Registry
     *
     * @return Int|Null $applicationId
     */
    public function getId()
    {
        $jobboardApplication = $this->registry->registry('jobboard_application');
        return $jobboardApplication ? $jobboardApplication->getId() : null;
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
