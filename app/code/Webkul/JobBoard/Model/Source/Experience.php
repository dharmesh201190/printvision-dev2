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
namespace Webkul\JobBoard\Model\Source;

class Experience implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve options array.
     *
     * @return array
     */
    public function toOptionArray()
    {

        $result = [];
       // $result[] = ['value' => '','label'=> 'Select'];
        $result[] = ['value' => '0','label'=> 'Fresher'];
        for ($i=1; $i<30; $i++) {
            $result[] = ['value' => "$i",'label'=> "$i"];
        }
        $result[] = ['value' => '30+','label'=> '30+'];

        return $result;
    }
}
