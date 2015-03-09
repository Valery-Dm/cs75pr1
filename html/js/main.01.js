/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/

// declare function names
var $, main, jsonpcall, showresult;

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
		$('#template').find('form :input:enabled:visible:first')
					  .focus();
	} else if (data.form) {
		// hide any message if shown
		$('.alerts').addClass('hidden');

		// form selector
		switch (data.form) {

		case 'form-quote':
			if (data.response.message === 'invalid quote') {
				// show alert
				$('#form-quote .alerts')
								.removeClass('hidden')
								.html(data.response.message);
			} else {
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
				// set focus on quantity field and select its value
				$('#template').find('form :input:enabled:visible:first')
							  .focus().select();
			}
			break;

		default:
			// show result message
			$('#quote-message')
					.removeClass('hidden')
					.find('h3').html(data.response.message);
			break;
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
		}/*,
		error: function (error) {
			alert(error);
		}*/
	});
};

/*
* Catch links and form submits,
* prevent their original behaviour,
* and calling for jsonpcall function instead
*/
main = function () {
	"use strict";
	var quotereg, quotename, data, id, quoteresp;
	// menu items functionality.
	// will ask server for html data
	// and rebuild the page
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
		var alert;

		// hide allerts
		$('form .alerts').addClass('hidden');
		// get form elements
		data = $(this).serializeArray();
		id = $(this).attr('id');

		if (id === 'form-quote') {
			// validate quote
			quotereg = /[^a-zA-Z]/;
			quotename = $('#quotes').val();
			if (quotereg.test(quotename)) {
				data = {'form': 'form-quote',
						'response': {'message': 'invalid quote'}};
				alert = true;
			}
		} else if (id === 'form-buy') {
			// don't call json if quantity is not valid
			if ($('#buytotal').val() <= 0) {
				data = {'form': 'form-buy',
						'response': {'message':
							"You didn't set shares quantity"}};
				alert = true;
			}
		} else if (id === 'form-sell') {
			// check valid input
			if (!$('#shares').val()) {
				data = {'form': 'form-sell',
						'response': {'message':
							"You didn't specify shares name"}};
				alert = true;
			} else if ($('#sharesq').val() <= 0) {
				data = {'form': 'form-sell',
						'response': {'message':
							"You didn't set shares quantity"}};
				alert = true;
			}
		}
		(alert) ? showresult(data) :
				  jsonpcall($(this).attr('id'), data);
	});
};

// get started
$(document).ready(main);