/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/


// declare function names
var main, jsonpcall, showresult;

/*
* Show result (data) from JSON query
*/
showresult = function (data) {
	"use strict";
	var alert, span, active, text, company, price, quote;

	if (data.body) {
		// replace current elements
		$('#template').html(data.body);
		$('#topmessage').html(data.message);
		document.title = 'CS75 finance: ' + data.title;
		// change active menu item
		span = $('.navbar-right .sr-only').detach();
		$('.navbar-right li.active').removeClass('active');
		active = '.navbar-right a[href="' + data.href + '"]';
		$(active).append(span).parent().addClass('active');
		// set focus on first input field
		$('#template').find('form :input:enabled:visible:first').focus();
	} else if (data.form) {
		switch (data.form) {
		case 'form-quote':
			if (data.response.data === 'invalid quote') {
				// show alert
				$('#form-quote .alerts')
								.removeClass('hidden')
								.html(data.response.message);
			} else {
				// hide any message shown
				$('#quote-message').addClass('hidden');
				// show and populate Buy form
				company = data.response.data[0].replace(/\"/g, "");
				price = data.response.data[1].replace(/\"/g, "");
				quote = data.response.data[2].replace(/\"/g, "");
				text = 'Current price for ' +
						company + ' is $<strong>' +
						price + '<strong/>';
				$('#quote-result')
							.removeClass('hidden')
							.find('p').html(text);
				$('#buyquote').val(quote);
				$('#buyname').val(company);
				$('#buyprice').val(price);
			}
			break;
		case 'form-buy':
			// hide result form
			$('#quote-result').addClass('hidden');
			// add message block
			$('#quote-message')
					.removeClass('hidden')
					.find('h3').html(data.response.message);
			break;
		}
	} else {
		// show alerts if any
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
		url: 'main_json.php',
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
		},
		error: function (error) {
			console.log(error);
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

	$('#navbar').on('click', 'a', function (event) {
		if ($(this).html().trim() !== 'Logout') {
			event.preventDefault();
			jsonpcall('menu', $(this).attr('href'));
		}
	});

	// catch form submition
	$('#template').on('submit', 'form', function (event) {
		// prevent sending POST query
		event.preventDefault();
		$('form .alerts').addClass('hidden');
		//console.log($(this).attr('id'));
		jsonpcall($(this).attr('id'), $(this).serializeArray());
	});
};

// get started
$(document).ready(main);