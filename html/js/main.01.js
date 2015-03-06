/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/


// declare function names
var main, validate;

validate = function (key, value) {
	"use strict";
	
	switch (key) {
		case 'username':
			// numbers and english letters only
			// min 3 and max 10 symbols long
			var namereg = /^[^\W_]{3,10}$/;
			return (namereg.test(value)) ? true : false;
		case 'password':
			// minimum 4 symbols, at least one number
			// and at least one uppercase character
			var passreg = /(?=.*\d)(?=.*[A-Z])[\S]{4,}$/;
			return (passreg.test(value)) ? true : false;
	}
};

main = function () {
	"use strict";
	
	// catch form submition
	$('form').on('submit', function (event) {
		// prevent sending POST query
		event.preventDefault();
		
		// validate input
		var form = $(this).serializeArray();
		console.log(form[1].value);
		if ($(this).attr('id') === "loginform") {
			if (!validate(form[0].name, form[0].value) ||
				!validate(form[1].name, form[1].value)) {
				$('#namealert').removeClass('hidden')
							   .html('wrong username or password');
			} else {
				$('#namealert').addClass('hidden');
			}
		} else if ($(this).attr('id') === "registerform") {
			if (!validate(form[0].name, form[0].value)) {
				$('#namealert').removeClass('hidden')
							   .html('requirements were not met');
			} else if (!validate(form[1].name, form[1].value)) {
				$('#namealert').addClass('hidden');
				$('#passalert').removeClass('hidden')
							   .html('requirements were not met');
			} else if (form[1].value !== form[2].value) {
				$('#passalert').addClass('hidden');
				$('#confalert').removeClass('hidden')
							   .html('passwords do not match');
			} else {
				$('#confalert').addClass('hidden');
				console.log('valid');
			}
		}
	});
};

// get started
$(document).ready(main);