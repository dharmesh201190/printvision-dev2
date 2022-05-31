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
namespace Webkul\JobBoard\Ui\Component\Application\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\App\RequestInterface;
use Webkul\JobBoard\Model\JobFactory;
 
class JobOptions implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $jobTree;

    /**
     * @var \Webkul\JobBoard\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @param JobFactory $jobFactory
     */
    public function __construct(
        JobFactory $jobFactory
    ) {
        $this->jobFactory = $jobFactory;
    }
 
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getJobTree();
    }
 
    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getJobTree()
    {
        if ($this->jobTree === null) {
            $jobData = [];
            $jobCollection = $this->jobFactory->create()->getCollection()
                                ->addFieldToSelect(["entity_id","designation"]);

            foreach ($jobCollection as $job) {
                $jobId = $job->getId();
                if (!isset($jobData[$jobId])) {
                    $jobData[$jobId] = [
                        'value' => $jobId
                    ];
                }
                $jobData[$jobId]['label'] = $job->getDesignation();
            }
            $this->jobTree = $jobData;
        }
        return $this->jobTree;
    }
}
