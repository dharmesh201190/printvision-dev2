/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    var sliderHeight = jQuery('.forcefullwidth_wrapper_tp_banner').height();
    var reassureHeight = jQuery('.reassure-topbanner').height();
    var mainSlideHeight = sliderHeight + reassureHeight;

    jQuery('.forcefullwidth_wrapper_tp_banner').animate({'height':'0'});
    jQuery('.reassure-topbanner').animate({'height':'0'});
    setTimeout(function(){
        jQuery("#topcontent").css('height', 'inherit');
        jQuery('.forcefullwidth_wrapper_tp_banner').animate({"height": sliderHeight}, { queue:false, duration:2000 });
        setTimeout(function(){
            jQuery('.reassure-topbanner').animate({"height": reassureHeight}, { queue:false, duration:2000 });
        });
    }, 1000);


    $('.cart-summary').mage('sticky', {
        container: '#maincontent',
        spacingTop: 51
    });

    $('.page-product-configurable .sidebar-additional').mage('sticky', { 
        container: '#maincontent'
    });

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');
    $('#store\\.links').find('#cdz-login-form-dropdown').remove();

    keyboardHandler.apply();
});
