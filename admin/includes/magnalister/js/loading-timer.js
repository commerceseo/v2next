(function($){
	var isSafari = /^((?!chrome).)*safari/i.test(navigator.userAgent);
	//console.log('isSafari', isSafari);
	$(document).ready(function() {
		$(".magnamain :submit").click(function (e) {
			if (isSafari) { // Normally you'd expect IE here, but this time Safai is like WTF!
				//console.log('Safari');
				e.preventDefault();
				var tehForm = $(this).parents('form');
				$.blockUI(jQuery.extend(blockUILoading, {
					onBlock: function () {
						//console.log('Submit');
						tehForm.submit();
					}
				}));
				return false;
			} else {
				setTimeout(function() { $.blockUI(blockUILoading); }, 1000);
				return true;
			}
		});
		$('.magnaTabs2 a').click(function (e) {
			if ($(this).attr('target') != '_blank') {
				if (isSafari) { // Same here.
					//console.log('Safari');
					e.preventDefault();
					var sHref = $(this).attr('href');
					$.blockUI(jQuery.extend(blockUILoading, {
						onBlock: function () {
							//console.log('Link');
							document.location.href = sHref;
						}
					}));
					return false;
				} else {
					setTimeout(function() { $.blockUI(blockUILoading); }, 1000);
					return true;
				}
			}
		});
		$(".magnamain select").change(function () {
			if (!$(this).parents().hasClass('config') && !$(this).is('#marketplacesync') && !$(this).parents().hasClass('attributesTable')) {
				setTimeout(function(){$.blockUI(blockUILoading);}, 1000);
			}
		});
	});
})(jQuery);