$('.numeric').change(function() {
	if ( !($.isNumeric($(this).val()))) {
		var input_id = '#' + $(this).attr('id');
		$(input_id).addClass('error');
		$('.submit').attr('disabled', 'disabled');
	} else if ($(this).val() < 0 || $(this).val() > 100 ) {
		var input_id = '#' + $(this).attr('id');
		$(input_id).addClass('warning');
	} else {
		var input_id = '#' + $(this).attr('id');
		$(input_id).removeClass('error');
		$(input_id).removeClass('warning');
		$('.submit').removeAttr("disabled");
	}
});