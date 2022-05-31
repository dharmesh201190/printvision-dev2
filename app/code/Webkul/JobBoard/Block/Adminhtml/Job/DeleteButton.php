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

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return Array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __('Delete Job'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(
                    "'.__('Are you sure you want to delete this Job ?'). '",
                    "'. $this->getDeleteUrl() .'"
                )',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
    
    /**
     * Get URL for Delete Action
     *
     * @return String
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
    }
}
