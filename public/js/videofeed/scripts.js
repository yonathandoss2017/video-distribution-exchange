(function($){

	$(window).ready(function() {
		
	});

	$( document ).ready(function() {
		registerHandlers();
	});

	var registerHandlers = function(){
		$('#import_type').on('change', function(){
			$val = $(this).val();
			if ('recent' == $val) {
				$('.import_recent_count').show();
			} else {
				$('.import_recent_count').hide();
			}
		});

	}


})(jQuery);

function ConfirmDelete() {
	var x = confirm(videofeed['confirm']);
	return (x) ? true : false;
}