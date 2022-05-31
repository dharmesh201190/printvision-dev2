require(['jquery', 'jquery/ui'], function($){
    $(document).ready(function() {
        $('.material_thickness select option:first-child').text('Select Thickness...');
        $('.size_options select option:first-child').text('Custom Size...');
        $('.measurement_units select option:first-child').text('MM');
        $('div.field.width input').attr('placeholder', 'Select Width...');
        $('div.field.height input').attr('placeholder', 'Select Height...');
        $('div.field.quantity input').attr('placeholder', '1');
        $('div.field.special_instructions_about_this_job textarea').attr('placeholder', 'Special instructions about this job...');
    });
});