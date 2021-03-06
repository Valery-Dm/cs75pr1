/*
* Version 01. Will be used for login and register pages.
* Improve user experience via local form validation,
* sending and getting JSON data.
*/

// declare function names
var $, main, validate, showresult, jsonpcall;

/*
* Take keys - username or password,
* and validate their values
*/
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
		// 4 charactes as minimum, at least one number
		// and at least one uppercase character
		passreg = /(?=.*\d)(?=.*[A-Z])[\S]{4,}$/;
		return (passreg.test(value)) ? true : false;
	}
};

/*
* Show result (data) from JSON query
*/
showresult = function (data) {
	"use strict";
	var alert;

	// if block of html code
	if (data.body) {
		// replace current elements
		$('#template').html(data.body);
		$('#topmessage').html(data.message);
		document.title = 'CS75 finance: ' + data.title;
		$('#template').find('form :input:enabled:visible:first').focus();
	} else if (data.url) {
		// login on success
		window.location = data.url;
	} else {
		// first hide any previous alert
		$('.alerts').addClass('hidden');
		// then show alerts given
		for (alert in data.alerts) {
			if (data.alerts.hasOwnProperty(alert)) {
				if (data.alerts[alert]) {
					$('#' + alert).removeClass('hidden')
								  .html(data.alerts[alert]);
				}
			}
		}
	}
};

/*
* Make JSONP request and get JSON data
*/
jsonpcall = function (callback, data) {
	$.ajax({
		url: 'index_json.php',
		jsonp: callback,
		dataType: 'jsonp',
		cache: false,
		crossDomain: true,
		data: {
			q: data,
			format: 'json'
		},
		success: function (response) {
			showresult(response);
		}
	});
};

/*
* Catch links and form submits,
* prevent their original behaviour,
* and calling for jsonpcall function instead
*/
main = function () {
	"use strict";

	// catch link
	$('#template')
		.on('click', 'a', function (event) {
		// prevent action and call for JSON
		event.preventDefault();
		return jsonpcall('link', $(this).attr('href'));
	})
	// check username 'on the fly',
	// while user is typing password
		.on('focus', '#registerform :input[id="password"]', function () {
		var value,
			data = {alerts:{ namealert: false }};
		value = $(':input:first').val();
		if (!validate('username', value)) {
			data.alerts.namealert = 'min 3, max 10 english\
									letters or digits';
			return showresult(data);
		} 
		return jsonpcall('checkname', 'username=' + value);
	})
	// catch form submition
		.on('submit', 'form', function (event) {
		// prevent sending POST query
		event.preventDefault();
		// prepare array and data for request
		var alert,
			alerts = {
				namealert: false,
				passalert: false,
				confalert: false
			},
			data = $(this).serialize(),
			form = $(this).serializeArray();

		// validate input
		if ($(this).attr('id') === "loginform") {
			if (!validate(form[0].name, form[0].value) ||
				!validate(form[1].name, form[1].value)) {
				// store alert
				alerts.namealert = 'wrong username or password';
			} else {
				return jsonpcall('login', data);
			}
		} else if ($(this).attr('id') === "registerform") {
			if (!validate(form[0].name, form[0].value)) {
				alerts.namealert = 'min 3, max 10 english\
									letters or digits';
			} else if (!validate(form[1].name, form[1].value)) {
				alerts.passalert = 'requirements were not met';
			} else if (form[1].value !== form[2].value) {
				alerts.confalert = 'passwords do not match';
			} else {
				return jsonpcall('register', data);
			}
		}
		return showresult({alerts: alerts});
	});
};

// get started
$(document).ready(main);