<?php
/**
 * Class InstallData
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_Faq
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Faq\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Sparsh\Faq\Model\FaqCategoryFactory;

/**
 * Class InstallData
 *
 * @category Sparsh
 * @package  Sparsh_Faq
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class InstallData implements InstallDataInterface
{
    /**
     * Primary faqCategoryFactory
     *
     * @var string
     */
    protected $faqCategoryFactory;

    /**
     * Faq constructor.
     *
     * @param FaqCategoryFactory $faqCategoryFactory  FaqCategoryFactory
     */
    public function __construct(
        FaqCategoryFactory $faqCategoryFactory
    ) {
        $this->faqCategoryFactory = $faqCategoryFactory;
    }

    /**
     * Install Data
     *
     * @param ModuleDataSetupInterface $setup   setup
     * @param ModuleContextInterface   $context context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $data = [
            'faq_category_name' => 'General',
            'faq_category_description' => '<p>This is a default category and can not be deleted or disabled. You can change the name according to your requirement.</p>',
            'is_active' => '1',
            'sort_order' => '1'
        ];
        $this->faqCategoryFactory->create()->addData($data)->save();
    }
}
