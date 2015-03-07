/*
*	Version 01. Will be used for login and register pages.
*	Improve user experience via local form validation.
*/

// declare function names
var main, validate, showresult, jsonpcall;

validate = function (key, value) {
	"use strict";
	var namereg, passreg;

	switch (key) {
		case 'username':
			// numbers and english letters only
			// min 3 and max 10 symbols long
			namereg = /^[^\W_]{3,10}$/;
			return (namereg.test(value)) ? true : false;
		case 'password':
			// minimum 4 symbols, at least one number
			// and at least one uppercase character
			passreg = /(?=.*\d)(?=.*[A-Z])[\S]{4,}$/;
			return (passreg.test(value)) ? true : false;
	}
};

showresult = function (alerts) {
	"use strict";

	var alert;
	// login on success
	if (alerts.url) {
		window.location = alerts.url;
	} else {
		// show alerts if any
		for (alert in alerts) {
			if (alerts[alert]) {
				$('#' + alert).removeClass('hidden')
							  .html(alerts[alert]);
			}
		}
	}
};

jsonpcall = function (form, data) {
	$.ajax({
		url: 'index_json.php',
		jsonp: form,
		dataType: 'jsonp',
		cache: false,
		crossDomain: true,
		data: {
			q: data,
			format: 'json'
		},
		success: function (response) {
			showresult(response);
		},
		error: function (error) {
			console.log(error);
		}
	});
};

main = function () {
	"use strict";

	// catch form submition
	$('form').on('submit', function (event) {
		// prevent sending POST query
		event.preventDefault();

		var alert,
			alerts = {
				url: false,
				namealert: false, 
				passalert: false, 
				confalert: false
			},
			data = $(this).serialize(),
			form = $(this).serializeArray();

		// validate input
		if ($(this).attr('id') === "loginform") {
			// first clear alert area 
			$('#namealert').addClass('hidden');
			if (!validate(form[0].name, form[0].value) ||
				!validate(form[1].name, form[1].value)) {
				// store alert
				alerts.namealert = 'wrong username or password';
			} else {
				return jsonpcall('login', data);
			}
		} else if ($(this).attr('id') === "registerform") {
			// first clear alerts if any 
			$('#namealert').addClass('hidden');
			$('#passalert').addClass('hidden');
			$('#confalert').addClass('hidden');
			if (!validate(form[0].name, form[0].value)) {
				// store alert
				alerts.namealert = 'min 3, max 10 english letters or digits';
			} else if (!validate(form[1].name, form[1].value)) {
				// store alert
				alerts.passalert ='requirements were not met';
			} else if (form[1].value !== form[2].value) {
				// store alert
				alerts.confalert = 'passwords do not match';
			} else {
				return jsonpcall('register', data);
			}
		}
		return showresult(alerts);
	});
};

// get started
$(document).ready(main);