$().ready(function() {

	$("#registerForm").validate({
		errorClass: 'errorField',
		errorElement: 'div',
		
		rules: {
			entryTitle: {
				required: true,
			},
			
			entryPost: {
				required: true
			}
		}
	});
});