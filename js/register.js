$().ready(function() {

	$("#registerForm").validate({
		errorClass: 'errorField',
		errorElement: 'div',
		
		rules: {
			emailAddress: {
				required: true,
				email: true
			},
			
			password: {
				required: true,
				minlength: 2
			},
			
			confirm_password: {
				required: true,
				minlength: 2,
				equalTo: "#register_password"
			},
			
			username: {
				required: true,
				minlength: 2
			}
			
		},
		messages: {
			emailAddress:"Please enter a valid email.",
			password: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 5 characters long."
			},
			confirm_password: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 5 characters long.",
				equalTo: "Please enter the same password as above."
			},
			username: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters"
			},
		}});
});