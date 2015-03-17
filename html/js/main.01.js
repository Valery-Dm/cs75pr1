/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/

// declare function names and timer
var main, call_for_template, draw_template,
	yql_quote, yql_draw_quote, parsedate,
	yql_table, yql_draw_table, finance_charts_call,
	finance_charts_json_callback, visual_selector;

/*
* Show result (data) from template query
*/
draw_template = function (data) {
	"use strict";
	var span, active; //text, company, price, quote;

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
			} /* else {
				// show and populate Buy form (temporary functionality)
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
			}*/
			break;

		default:
			// show result message
			$('#quote-message')
					.removeClass('hidden')
					.find('h3').html(data.response.message);
			break;
		}
	}
	$('#pageload').addClass('hidden');
};

/*
* Make JSONP request and get JSON data
* from our server. Callback identifies the template
* we need to get back as HTML
* and Data contains submitted form data for DB query.
*/
call_for_template = function (callback, data) {
	"use strict";
	$('#pageload').removeClass('hidden');
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
			draw_template(response);
		}/*,
		error: function (error) {
			alert(error);
		}*/
	});
};

/**
* Draw table with Google visualisation API
* and data returned by YQL query
**/
yql_draw_table = function (data) {
	"use strict";
	//console.log(data);
	if (data.query.count === 0) {
		$('#rchart').html('No data');
	} else {
		var day, content = [],
			quote = data.query.results.quote;
		if (data.query.count === 1) {
			content.push([	quote.Date,
							parseFloat(quote.Low),
							parseFloat(quote.Open),
							parseFloat(quote.Close),
							parseFloat(quote.High)		]);
		} else {
			for (day in quote) {
				if (quote.hasOwnProperty(day)) {
					content.push([	quote[day].Date,
									parseFloat(quote[day].Low),
									parseFloat(quote[day].Open),
									parseFloat(quote[day].Close),
									parseFloat(quote[day].High)		]);
				}
			}
		}
		$.getScript("https://www.google.com/jsapi", function () {
			google.load("visualization", "1", {callback: drawTable, packages: ["table"]});
			function drawTable() {
				var data = new google.visualization.DataTable(),
					table = new google.visualization.Table($('#rchart')[0]),
					options = {
						width: '100%',
						page: 'enable',
						pageSize: 20,
						sortColumn: 0,
						sortAscending: false
					};
				data.addColumn('string', 'Date');
				data.addColumn('number', 'Low');
				data.addColumn('number', 'Open');
				data.addColumn('number', 'Close');
				data.addColumn('number', 'High');
				data.addRows(content);
				table.draw(data, options);
			}
		});
	}
	$("#vizload").addClass('hidden');
	// for being late queries change selector
	$('#rselect li').removeClass('select');
	$('#rselect:last-child').addClass('select');
};

/**
* Get quotes history. 
* Arguments are - quote name, start and end dates (yyyy-mm-dd).
**/
yql_table = function (quote, start, end) {
	"use strict";
	//https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20
	//yahoo.finance.historicaldata%20where%20symbol%20%3D%20%22AAPL%22%20and%20
	//startDate%20%3D%20%222013-02-01%22%20and%20endDate%20%3D%20%222013-02-25%22&
	//format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.historicaldata WHERE symbol = "'
				+ quote + '" AND startDate = "' + start + '" AND endDate = "' + end + '"',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yql_draw_table'
		};
	$("#vizload").removeClass('hidden');
	$.ajax({
		url: url,
		jsonp: 'yql',
		dataType: 'jsonp',
		cache: false,
		crossDomain: true,
		data: options
	});
};

/**
* Helper function for date string
* returned by Yahoo.
* Will create yyyy-mm-dd form
**/
parsedate = function (format, input) {
	"use strict";
	var result, d;
	if (input > 1e9) {
		result = new Date(input * 1000).toISOString();
		if (format === 'time') {
			result = result.slice(11, 16);
		}
		result = result.slice(0, 10);
	} else {
		d = input.toString();
		result =
			d.slice(0, 4) + "-" +
			d.slice(4, 6) + "-" +
			d.slice(6);
	}
	return result;
};

/**
* Function name taken from Yahoo callback.
* So possibly it won't be called someday,
* to fix this look into Network->Response tab.
**/
finance_charts_json_callback = function (data) {
	"use strict";
	if (!data.errorid) {
		var node, rfooter,
			interval, lastprice,
			min, max, period,
			format = 'date',
			table = [],
			nodes = data.series,
			info = data.meta,
			ranges = data.ranges,
			labels = Math.floor(nodes.length / 5);
		// for ranges 1d and 5d
		if (info.unit === 'MIN') {
			interval = 'Timestamp';
			lastprice = info.previous_close;
			// for 1d only
			if (!data['TimeStamp-Ranges']) {
				format = 'time';
			}
		} else {
			interval = 'Date';
			lastprice = info.previous_close_price;
		}
		min = parsedate(format, data[interval].min);
		max = parsedate(format, data[interval].max);
		period = min + ' &mdash; ' + max;
		rfooter =
				'<ul>' +
					'<li>Open min: ' + ranges.open.min + '</li>' +
					'<li>Open max: ' + ranges.open.max + '</li>' +
					'<li>Close min: ' + ranges.close.min + '</li>' +
					'<li>Close max: ' + ranges.close.max + '</li>' +
				'</ul><ul>' +
					'<li>Low min: ' + ranges.low.min + '</li>' +
					'<li>High max: ' + ranges.high.max + '</li>' +
					'<li>Volume min: ' + ranges.volume.min + '</li>' +
					'<li>Volume max: ' + ranges.volume.max + '</li>' +
				'</ul>';
		$('#rrange').html(period);
		$('#rfooter').html(rfooter);
		for (node in nodes) {
			if (nodes.hasOwnProperty(node)) {
				table.push([	parsedate(format, nodes[node][interval]),
								parseFloat(nodes[node].low),
								parseFloat(nodes[node].open),
								parseFloat(nodes[node].close),
								parseFloat(nodes[node].high)		]);
			}
		}

		$.getScript("https://www.google.com/jsapi", function () {
			google.load("visualization", "1", {callback: drawChart, packages: ["corechart"]});
			function drawChart() {
				var data = google.visualization.arrayToDataTable(table, true),
					options = {
						legend: 'none',
						title: 'Previous close price ' + lastprice,
						theme: {
							chartArea: {width: '90%', height: '80%', left: '10%'},
							titlePosition: 'in',
							titleTextStyle: {color: '#777'},
							fontSize: 12,
							series: {0: {color: '#0af'}},
							hAxis: {
								textPosition: 'out',
								showTextEvery: labels,
								textStyle: {color: '#777'}
							},
							vAxis: {
								textPosition: 'out',
								textStyle: {color: '#777'}
							}
						}
					},
					// google chart is not responsive, 
					// so when you change browser window dimensions
					// it won't be changed until next function call
					chart = new google.visualization.CandlestickChart($('#rchart')[0]);
				chart.draw(data, options);
			}
		});
		$("#vizload").addClass('hidden');
		$('#rselect li').removeClass('select');
		$('#rselect:first-child').addClass('select');
	}
};

/**
* Call for chart data (without YQL now)
**/
finance_charts_call = function (quote, range) {
	"use strict";
	var url = 'http://chartapi.finance.yahoo.com/instrument/1.0/'
			 + quote + '/chartdata;type=quote;range=' + range + '/json/';
	$("#vizload").removeClass('hidden');
	$.ajax({
		url: url,
		jsonp: 'chart',
		dataType: 'jsonp',
		cache: false,
		crossDomain: true
		// No success function here.
		// This query returns as finance_charts_json_callback object.
		// So I created draw function with the same name.
	});
};

/**
* Will be called on clicks for Candle chart
* History table selector and ranges Menu
**/
visual_selector = function (chart, quote, range) {
	"use strict";
	if (chart === 'Candle chart') {
		finance_charts_call(quote.toUpperCase(), range);
	} else {
		var date = new Date(),
			end = date.toISOString().substring(0, 10),
			start;
		switch (range) {
		case '1d':
			date.setDate(date.getDate() - 1);
			break;
		case '5d':
			date.setDate(date.getDate() - 5);
			break;
		case '1m':
			date.setMonth(date.getMonth() - 1);
			break;
		case '3m':
			date.setMonth(date.getMonth() - 3);
			break;
		case '5m':
			date.setMonth(date.getMonth() - 5);
			break;
		case '1y':
			date.setFullYear(date.getFullYear() - 1);
			break;
		case '5y':
			date.setFullYear(date.getFullYear() - 5);
			break;
		}
		start = date.toISOString().substring(0, 10);
		// Clear footer from Candle chart results
		$('#rfooter').html('');
		// Change 'from - to' label
		$('#rrange').html(start + ' &mdash; ' + end);
		yql_table(quote.toUpperCase(), start, end);
	}
};

/**
* Treat YQL result for Quote
**/
yql_draw_quote = function (data) {
	"use strict";
	$("#quoteload").addClass('hidden');
	if (typeof data.error !== 'undefined' ||
			data.query.results.quote.LastTradePriceOnly === '0.00' ||
			data.query.results.quote.LastTradePriceOnly === null) {
		draw_template({'form': 'form-quote',
					'response': {'message': 'invalid quote'}});
	} else {
		var date = new Date().toUTCString(),
			quote = data.query.results.quote,
			color = (quote.Change >= 0) ? 'green' : 'red',
			price = quote.LastTradePriceOnly +
					'<span id="rchange" style="color: ' +
					color + '"> ' + quote.Change + '</span>';
		$('#rname').html(quote.Name);
		$('#rquote').html(quote.symbol);
		$('#rlegend').html(quote.StockExchange + ": "
						 + quote.Symbol + ' - ' + date);
		$('#rprice').html(price);
		// fillout buy form (though it's hidden for now)
		$('#buyquote').val(quote.symbol);
		$('#buyname').val(quote.Name);
		$('#buyprice').val(price);
		$('#form-buy p').html('Current price for ' + quote.Name + ' is $' + price);
		// hide loading gif and show results
		$('#result-block').removeClass('hidden');
	}
};

/**
* Get quote via YQL query. 
* Prepared for query multiple quotes passed as array
**/
yql_quote = function (quotes) {
	"use strict";
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.quote WHERE symbol in ("' + quotes.join() + '")',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yql_draw_quote'
		};

	// show loading gif
	$("#quoteload").removeClass('hidden');
	// Insert script tag into page is one way to query Yahoo
	// $('<script type="text/javascript" src="' + url 
	//  + $.param(options) + '"></\script>').appendTo(document.body);
	//console.log(quotes[0]);
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
};

/*
* Catch links and form submits,
* prevent their original behaviour,
* and calling for call_for_template function instead
*/
main = function () {
	"use strict";
	var quotereg, quotename, data, id, quotetimer;

	// menu items functionality.
	// will ask server for html data
	// and rebuild the page
	$('#navbar').on('click', 'a', function (event) {
		if ($(this).html().trim() !== 'Logout') {
			event.preventDefault();
			// stop timer if started
			clearInterval(quotetimer);
			call_for_template('menu', $(this).attr('href'));
		}
	});
	// Chart / table selector
	$('#template').on('click', '#rselect li', function () {
		//console.log($(this).html());
		$('#rselect li').removeClass('active');
		$(this).addClass('active');
		visual_selector($(this).html(),
						$('#rquote').html(),
						$('#rmenu .active').html());
	});
	// Ranges menu
	$('#template').on('click', '#rmenu li', function () {
		//console.log($(this).html());
		$('#rmenu li').removeClass('active');
		$(this).addClass('active');
		visual_selector($('#rselect .active').html(),
						$('#rquote').html(),
						$(this).html());
	});
	// Add to porfolio button
	$('#template').on('click', '#rbtn', function () {
		$('#quote-result').removeClass('hidden');
		$('#buytotal').focus().select();
	});
	// catch form submition
	$('#template').on('submit', 'form', function (event) {
		// prevent sending POST query
		event.preventDefault();
		var alert;
		$('#template')
			.find('form :input:enabled:visible:first')
			.focus();
		
		// hide allerts
		$('form .alerts').addClass('hidden');
		// get form elements
		data = $(this).serializeArray();
		id = $(this).attr('id');

		if (id === 'form-quote') {
			// validate quote
			quotereg = /[^a-zA-Z]/;
			quotename = $('#quotes').val();
			if (quotename === '' || quotereg.test(quotename)) {
				data = {'form': 'form-quote',
						'response': {'message': 'invalid quote'}};
				alert = true;
			} else {
				$("#quoteload").removeClass('hidden');
				$('#quote-result').addClass('hidden');
				// update price with 10 seconds intervals
				quotetimer = setInterval(function () {
					yql_quote([quotename.toUpperCase()]);
				}, 10000);
				//yql_quote([quotename.toUpperCase()]);
				// Draw default visualization
				visual_selector('Candle chart',
								quotename.toUpperCase(),
								'1d');
				// stop here
				return;
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
		return (alert) ? draw_template(data) : call_for_template($(this).attr('id'), data);
	});

};

// get started
$(document).ready(main);