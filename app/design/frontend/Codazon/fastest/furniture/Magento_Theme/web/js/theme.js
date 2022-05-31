/**
 * Copyright Â© Magento, Inc. All rights reserved.
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
    
    var headermain = jQuery('.page-header').height();
    var breadcrumbs = jQuery('.breadcrumbs').height();
    var headerHeight = headermain + breadcrumbs;
    var footerHeight = jQuery('.footer').height();
    var sidebarHeight = jQuery('.page-product-configurable .sidebar-additional').height();
    var productInfoHeight = jQuery('.product-main-content').height();
    var s = jQuery(".sidebar");
    jQuery(window).scroll(function() {
        var contentHeight = jQuery('#maincontent').height();
        var dsfghb = (contentHeight - sidebarHeight) + headerHeight;
        var windowpos = jQuery(window).scrollTop();
    
        var offset = jQuery(document).scrollTop() - sidebarHeight;
        
        if (windowpos >= headerHeight & windowpos <= contentHeight) {
    
            s.addClass("stick");
            var sxdfhr = contentHeight - sidebarHeight;
            s.css('top', sxdfhr + 'px');
    
            if (windowpos >= headerHeight & windowpos <= dsfghb) {
    
                s.addClass("stick");
                var testsdfbef = jQuery(document).scrollTop() - headerHeight;
                s.css('top', testsdfbef + 'px');
    
            }
        }
        jQuery(".item.title").click(function(){
            var test = jQuery(".product.data.items").height();
            var contentHeight = productInfoHeight + test;
            console.log(contentHeight);
            var dsfghb = (contentHeight - sidebarHeight) + headerHeight;
    
            var s = jQuery(".sidebar");
            var windowpos = jQuery(window).scrollTop();
    
            var offset = jQuery(document).scrollTop() - sidebarHeight;
            
            if (windowpos >= headerHeight & windowpos <= contentHeight) {
    
                s.addClass("stick");
                var sxdfhr = contentHeight - sidebarHeight;
                s.css('top', sxdfhr + 'px');
    
                if (windowpos >= headerHeight & windowpos <= dsfghb) {
    
                    s.addClass("stick");
                    var testsdfbef = jQuery(document).scrollTop() - headerHeight;
                    s.css('top', testsdfbef + 'px');
    
                }
            }
        });
    });
    

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');
    $('#store\\.links').find('#cdz-login-form-dropdown').remove();

    keyboardHandler.apply();
});