/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_JobBoard
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    "mage/template",
    "mage/mage",
    'mage/validation'
    ],
    function ($,$t,modal,mageTemplate) {
        'use strict';
        $.widget('mage.jobBoard', {
            options: {},
            _create: function () {
                var self = this;
                var jobModal = {
                    type: "popup",
                    autoOpen: true,
                    responsive: true,
                    innerScroll: true,
                    modalClass: 'wk-jobboard-popup wk-job-application-popup-opened',
                    buttons: false,
                    clickableOverlay:false
                };

                $(document).ready(function(){
                        $('ul.tabs li').click(function(){
                        var tab_id = $(this).attr('data-tab');
                        $('ul.tabs li').removeClass('current');
                        $('.tab-content').removeClass('current');
                        $(this).addClass('current');
                        $("."+tab_id).addClass('current');
                    })
                })

                $("#btnsubmit").on('click', function() {
                    if ($("#job-form").validation('isValid')) {
                        $('body').trigger('processStart');
                        $("#job-form").submit();
                    }
                });

                $(".job-profile .apply-job").on("click",function () {
                    var jobId = $(this).attr("data-jobId");
                    var jobName = $("#job-"+jobId).find(".name").text();
                    openJobModal(jobId,jobName);
                });
                $(".job-profile .view-job").on("click",function () {
                    var jobId = $(this).attr("data-jobId");
                    openViewJobModal(jobId);
                });

                function openViewJobModal(jobId)
                {
                    let templ = $('#job-view-modal-popup').html();
                    $('#job-view-modal-click-popup').html('');
                    $('#job-view-modal-click-popup').html(templ);
                    $('#job-view-modal-click-popup').find('.description-pop').html(self.options.jobs[jobId].description);
                    $('#job-view-modal-click-popup').find('.eligibility-pop').html(self.options.jobs[jobId].eligibility);
                    $('#job-view-modal-click-popup').find('.location-pop').html(self.options.jobs[jobId].location);
                    $('#job-view-modal-click-popup').find('.skills-pop').html(self.options.jobs[jobId].skills);
                    var jobViewPopup = modal(jobModal, $('.job-view-modal-popup'));
                }

                function openJobModal(jobId,jobName)
                {
                    var jobPopup = modal(jobModal, $('.job-modal-popup'));
                    $('.job-modal-popup').on("modalopened", function () {
                        $("form.job-form input#job-position").val(jobName);
                        $("form.job-form input#job").val(jobId);
                    });
                }
            }
        });
        return $.mage.jobBoard;
    }
);
