/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/

// declare function names
var $, main, jsonpcall, showresult, yqlcall, yqlcallback, yqlchart, yqlchartback;

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
/**
* Treat YQL result
**/
yqlcallback = function (data) {
	if (typeof data.error != 'undefined' ||
		data.query.results.quote.LastTradePriceOnly === '0.00') {
		showresult({'form': 'form-quote',
					'response': {'message': 'invalid quote'}});
	} else {
		var content = '<ul>';
		$.each(data.query.results.quote, function (name, value) {
			content += '<li><strong>'+name+'</strong>: '+value+'</li>';
		 });
		content += '</ul>';
		$('#result-list').html(content);
		$(".loading").addClass('hidden');
	}
};
yqlchartback = function (data) {
	var day, param, table = [], quote = data.query.results.quote;
	//console.log(data);
	for (day in quote) {
		//console.log(quote[day]);
		table.push([	quote[day].Date, 
						parseFloat(quote[day].Low), 
						parseFloat(quote[day].Open), 
						parseFloat(quote[day].Close), 
						parseFloat(quote[day].High)		]);
	}
	console.log(table);
	$.getScript("https://www.google.com/jsapi", function(){
		google.load("visualization", "1", {callback:drawChart, packages:["corechart"]});
		//google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable(table, true),
				options = {
				  	legend:'none'
				},
				// google chart is not responsive, 
				// so when you change browser window dimensions
				// it will be changed on next function call
				div = $('<div id="rchart" class="clear" style="width: 100%"></div>').appendTo('.container'),
				chart = new google.visualization.CandlestickChart($('#rchart')[0]);
			chart.draw(data, options);
		}
	});
	$(".loading").addClass('hidden');
};
/**
* Get quotes history. 
* Arguments are - quote name, start and end dates (YYYY-MM-DD).
**/
yqlchart = function (quote, start, end) {
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.historicaldata WHERE symbol = "'
				+quote+'" AND startDate = "'+start+'" AND endDate = "'+end+'"',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yqlchartback'
		};
	$(".loading").removeClass('hidden');
	$.ajax({
		url: url,
		jsonp: 'yql',
		dataType: 'jsonp',
		cache: false,
		crossDomain: true,
		data: options
	});
}
//http://chartapi.finance.yahoo.com/instrument/1.0/FB/chartdata;type=quote;range=1d/json/
//https://github.com/yql/yql-tables/tree/master/yahoo/finance
/**
* Get quote via YQL query. 
* Prepared for query multiple quotes passed as array
**/
yqlcall = function (quotes) {
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.quote WHERE symbol in ("'+quotes.join()+'")',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yqlcallback'
		};
//https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20
//yahoo.finance.historicaldata%20where%20symbol%20%3D%20%22AAPL%22%20and%20
//startDate%20%3D%20%222013-02-01%22%20and%20endDate%20%3D%20%222013-02-25%22&
//format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=
	// show loading gif
	$(".loading").removeClass('hidden');
	// Insert script tag into page is one way to query Yahoo
	// $('<script type="text/javascript" src="' + url 
	//  + $.param(options) + '"></\script>').appendTo(document.body);

	// JSONP request. Yahoo calls for respond function
	// which name is specified in data.callback (argument in the url).
	// Success function inside ajax won't work.
	$.ajax({
		url: url,
		jsonp: 'yql',
		dataType: 'jsonp',
		cache: false,
		crossDomain: true,
		data: options
	});
}

/*
* Catch links and form submits,
* prevent their original behaviour,
* and calling for jsonpcall function instead
*/
main = function () {
	"use strict";
	var quotereg, quotename, data, id;
	
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
			} else {
				//return yqlcall([quotename.toUpperCase()]);
				return yqlchart(quotename.toUpperCase(),'2014-12-01', '2014-12-25');
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