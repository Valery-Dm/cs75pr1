/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/


// declare function names
var main, jsonpcall, showresult;

showresult = function (data) {
	"use strict";
	var alert;

	if (data.body) {
		console.log(data.body);
	} else if (data.url) {
		// this part for logout
		window.location = data.url;
	} else {
		// show data if any
		for (alert in data) {
			if (data.hasOwnProperty(alert)) {
				if (data[alert]) {
					$('#' + alert).removeClass('hidden')
								  .html(data[alert]);
				}
			}
		}
	}
};

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
		},
		error: function (error) {
			console.log(error);
		}
	});
};

main = function () {
	"use strict";

	$('.navbar a').on('click', function (event) {
		//event.preventDefault();
		console.log($(this).serialize());
		jsonpcall('link', $(this).attr('href'));
		
	});

	// catch form submition
	$('form').on('submit', function (event) {
		// prevent sending POST query
		//event.preventDefault();
		
	});
};

// get started
$(document).ready(main);