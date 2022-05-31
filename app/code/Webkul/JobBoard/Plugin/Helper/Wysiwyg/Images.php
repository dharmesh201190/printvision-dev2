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

namespace Webkul\Jobboard\Plugin\Helper\Wysiwyg;

class Images
{
    public function __construct(
        \Magento\Cms\Helper\Wysiwyg\Images $image
    ) {
        $this->image = $image;
    }

    public function aroundGetImageHtmlDeclaration(
        \Magento\Cms\Helper\Wysiwyg\Images $subject,
        \Closure $proceed,
        $filename,
        $renderAsTag = false
    ) {
        $result = $proceed($filename, $renderAsTag);
        $fileUrl = $this->image->getCurrentUrl() . $filename;
        if ($renderAsTag) {
            $html = sprintf('<img src="%s" alt="" />', $fileUrl);
        } else {
            if ($this->image->isUsingStaticUrlsAllowed()) {
                $html = $fileUrl;
            } else {
                $html = $fileUrl;
            }
        }
        return $html;
    }
}
