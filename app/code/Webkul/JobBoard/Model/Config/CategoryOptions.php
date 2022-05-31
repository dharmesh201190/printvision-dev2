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
namespace Webkul\JObBoard\Model\Config;

use Webkul\JobBoard\Model\CategoryFactory;

class CategoryOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Webkul\JobBoard\Model\CategoryFactory
     */
    protected $categoryFactory;
    
    /**
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }
    
    /**
     * Create Array for Category of Id & Name
     *
     * @return Array $data
     */
    public function toOptionArray()
    {
        $jobCategoryCollection = $this->categoryFactory->create()
                                      ->getCollection();

        $data = [];
        foreach ($jobCategoryCollection as $jobCategory) {
            $data[] = ['value' => $jobCategory->getId(), 'label' => $jobCategory->getName()];
        }
        
        return $data;
    }
}
