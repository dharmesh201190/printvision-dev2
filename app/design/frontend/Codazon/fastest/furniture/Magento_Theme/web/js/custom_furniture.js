require([
"jquery",
'matchMedia',
"jquery/ui"
], function($){
	$(document).ready(function () {
		$(".search-icon").click(function(){
	    	$(".search-wrapper").toggleClass("active");
		});
		mediaCheck({ 
	        media: '(max-width: 767px)',
	        entry: function () {
		        $(".product-summery").appendTo($(".product-main-content"));
		        $(".search-icon").click(function(){
			    	$(".search-form-container").slideToggle();
				});
	        }
	    });
    });

	/* Scroll to Top after Adding Product to Cart */
    $('[data-block="minicart"]').on('contentLoading', function (event) {    
        $('[data-block="minicart"]').on('contentUpdated', function ()  {
            $('html, body').animate({scrollTop:0}, 'slow');
        });
    });

    //Aatish Start
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 160 ) {
            $("ul.contact_header").css('display', "none");
        }else{
            $("ul.contact_header").css('display', "block");
        }
    });
    //Aatish End

});