$('#login').click(function() {
	$(this).css('background-color', '');
	$(this).off('mouseenter mouseleave');
	$('#login-form').show();
	
});

$('li').hover(
	function() {
		$(this).css('background-color', '#92A6DB')
    }, function() {
        $(this).css('background-color', '')
});