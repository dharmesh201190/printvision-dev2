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
namespace Webkul\JobBoard\Block\Adminhtml\Job;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Search\Controller\RegistryConstants;

class GenericButton
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * Undocumented function
     *
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
     * Get Job Id from Registry
     *
     * @return Int|Null $jobId
     */
    public function getId()
    {
        $job = $this->registry->registry('jobboard_job');
        return $job ? $job->getId() : null;
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
