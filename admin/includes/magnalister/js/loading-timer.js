(function($){
	$(document).ready(function() {
		$(".magnaTabs2 :submit").click(function () {
			setTimeout(function(){$.blockUI(blockUILoading);}, 1000);
		});
		$('.magnaTabs2 a').click(function () {
			if ($(this).attr('target') != '_blank' && !$(this).hasClass('ml-js-noBlockUi')) {
				setTimeout(function(){$.blockUI(blockUILoading);}, 1000);
			}
		});
		$(".magnamain select").change(function () {
			if (!$(this).parents().hasClass('config') && !$(this).is('#marketplacesync') && !$(this).parents().hasClass('attributesTable')) {
				setTimeout(function(){$.blockUI(blockUILoading);}, 1000);
			}
		});
	});
})(jQuery);