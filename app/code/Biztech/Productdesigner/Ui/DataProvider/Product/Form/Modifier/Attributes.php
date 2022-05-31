<?php

namespace Biztech\Productdesigner\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Locator\LocatorInterface;

class Attributes extends AbstractModifier
{
    /**
     * @var Magento\Framework\Stdlib\ArrayManager
     */
    private $arrayManager;
    protected $locator;
    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * modifyData
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        if(!empty($this->locator->getProduct()->getDesignerProductType())){
            $attributes =  array('design_templates_category','pre_loaded_template','enable_handles');
            foreach ($attributes as $key => $value) {
                $path = $this->arrayManager->findPath($value, $meta, null, 'children');
                $meta = $this->arrayManager->set(
                    "{$path}/arguments/data/config/disabled",
                    $meta,
                    true
                );
            }
        }
        return $meta;
    }

}