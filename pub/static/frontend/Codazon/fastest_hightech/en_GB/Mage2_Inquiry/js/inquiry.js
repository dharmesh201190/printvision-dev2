/**
 * Mage2developer
 * Copyright (C) 2021 Mage2developer
 *
 * @category Mage2developer
 * @package Mage2_Inquiry
 * @copyright Copyright (c) 2021 Mage2developer
 * @author Mage2developer <mage2developer@gmail.com>
 */

require([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    jQuery(document).ready(function () {
        jQuery(".question-listing").hide();
        jQuery(".view-question label").click(function () {
            jQuery(".question-listing").slideToggle("slow");
        });
    });
})
