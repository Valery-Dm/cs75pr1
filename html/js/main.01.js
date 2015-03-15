/*
*	Version 01. Will be used for inner pages (i.e. after logon).
*	Improve user experience via JSON calls to server,
*	local form validation etc.
*/

// declare function names
var $, main, call_for_template, draw_template, yql_quote, yql_draw_quote, parsedate,
	yql_table, yql_draw_table, finance_charts_call, finance_charts_json_callback;

/*
* Show result (data) from template query
*/
draw_template = function (data) {
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
* from our server. Callback identifies the template
* we need to get back as HTML
* and Data contains submitted form data for DB query.
*/
call_for_template = function (callback, data) {
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
* Treat YQL result for Quote
**/
yql_draw_quote = function (data) {
	if (typeof data.error != 'undefined' ||
		data.query.results.quote.LastTradePriceOnly === '0.00') {
		draw_template({'form': 'form-quote',
					'response': {'message': 'invalid quote'}});
	} else {
		var date = new Date().toUTCString(), 
			quote = data.query.results.quote, 
			price, color;
			
		/*
		var	content = '<ul>';
		$.each(quote, function (name, value) {
			content += '<li><strong>'+name+'</strong>: '+value+'</li>';
		 });
		content += '</ul>';
		$('#result-list').html(content);
		*/
		$('#result-block').removeClass('hidden');
		$(".loading").addClass('hidden');

		color = (quote.Change >= 0) ? 'green' : 'red';
		price = quote.LastTradePriceOnly +
					'<span id="rchange" style="color: ' +
					color + '"> ' + quote.Change + '</span>';
		$('#rname').html(quote.Name);
		$('#rquote').html(quote.symbol);
		$('#rlegend').html(quote.StockExchange + ": "
						  +quote.Symbol + ' - ' + date);
		$('#rprice').html(price);

		finance_charts_call(quote.symbol.toUpperCase(),'1m');
	}
};
/**
* Draw table with Google visualisation API
* and data returned by YQL query
**/
yql_draw_table = function (data) {
	var day, content = [], quote = data.query.results.quote;
	for (day in quote) {
		content.push([	quote[day].Date, 
						parseFloat(quote[day].Low), 
						parseFloat(quote[day].Open), 
						parseFloat(quote[day].Close), 
						parseFloat(quote[day].High)		]);
	}
	$.getScript("https://www.google.com/jsapi", function(){
		google.load("visualization", "1", {callback:drawTable, packages:["table"]});
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
	$(".loading").addClass('hidden');
};
/**
* Get quotes history. 
* Arguments are - quote name, start and end dates (YYYY-MM-DD).
**/
yql_table = function (quote, start, end) {
	//https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20
	//yahoo.finance.historicaldata%20where%20symbol%20%3D%20%22AAPL%22%20and%20
	//startDate%20%3D%20%222013-02-01%22%20and%20endDate%20%3D%20%222013-02-25%22&
	//format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.historicaldata WHERE symbol = "'
				+quote+'" AND startDate = "'+start+'" AND endDate = "'+end+'"',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yql_draw_table'
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
/**
* Helper function for date string
* returned by Yahoo.
* Will create yyyy-mm-dd form
**/
parsedate = function (input) {
	var d = input.toString();
	return d.slice(0,4) + "-" + d.slice(4,6) + "-" + d.slice(6);
}
/**
* Function name taken from Yahoo callback.
* So possibly it won't be called someday,
* to fix this look into Network->Response tab.
**/
finance_charts_json_callback = function (data) {
	console.log(data);
	var node, rfooter, charttheme,
		table = [], nodes = data.series,
		info = data.meta,
		ranges = data.ranges,
		period = parsedate(data.Date.min) + ' &mdash; '
				 + parsedate(data.Date.max);
	rfooter = 
			'<ul>'+
				'<li>Open min: '+ ranges.open.min +'</li>'+
				'<li>Open max: '+ ranges.open.max +'</li>'+
				'<li>Close min: '+ ranges.close.min +'</li>'+
				'<li>Close max: '+ ranges.close.max +'</li>'+
			'</ul><ul>'+
				'<li>Low min: '+ ranges.low.min +'</li>'+
				'<li>High max: '+ ranges.high.max +'</li>'+
				'<li>Volume min: '+ ranges.volume.min +'</li>'+
				'<li>Volume max: '+ ranges.volume.max +'</li>'+
			'</ul>';
	$('#rrange').html(period);
	$('#rfooter').html(rfooter);
	charttheme = {
		chartArea: {width: '100%', height: '70%', left: 25},
		titlePosition: 'in', titleTextStyle: {color: '#555'},
		fontSize: 12,
		series: {0:{color: '#0af', visibleInLegend: false}},
		hAxis: {textPosition: 'out',
				showTextEvery: 4,
				textStyle: {color: '#999'}}, 
		vAxis: {textPosition: 'out',
				textStyle: {color: '#999'}}
	};
	for (node in nodes) {
		table.push([	parsedate(nodes[node].Date), 
						parseFloat(nodes[node].low), 
						parseFloat(nodes[node].open), 
						parseFloat(nodes[node].close), 
						parseFloat(nodes[node].high)		]);
	}

	$.getScript("https://www.google.com/jsapi", function(){
		google.load("visualization", "1", {callback:drawChart, packages:["corechart"]});

		function drawChart() {
			var data = google.visualization.arrayToDataTable(table, true),
				options = {
				  	legend: 'none',
					title: 'Previous close price ' + info.previous_close_price,
					theme: charttheme
				},
				// google chart is not responsive, 
				// so when you change browser window dimensions
				// it won't be changed until next function call
				//div = $(rchart).appendTo('.container'),
				chart = new google.visualization.CandlestickChart($('#rchart')[0]);
			chart.draw(data, options);
		}
	});
	$(".loading").addClass('hidden');
}
/**
* Call for chart data (without YQL now)
**/
finance_charts_call = function (quote, range) {
	var url = 'http://chartapi.finance.yahoo.com/instrument/1.0/'
			 + quote + '/chartdata;type=quote;range=' + range + '/json/';
	$(".loading").removeClass('hidden');
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
}
/**
* Get quote via YQL query. 
* Prepared for query multiple quotes passed as array
**/
yql_quote = function (quotes) {
	var url = 'https://query.yahooapis.com/v1/public/yql',
		options = {
			q: 'SELECT * FROM yahoo.finance.quote WHERE symbol in ("'+quotes.join()+'")',
			format: 'json',
			diagnostics: 'true',
			env: 'store://datatables.org/alltableswithkeys',
			callback: 'yql_draw_quote'
		};

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
* and calling for call_for_template function instead
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
			call_for_template('menu', $(this).attr('href'));
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
				return yql_quote([quotename.toUpperCase()]);
				//return yql_table(quotename.toUpperCase(),'2014-12-01', '2014-12-25');
				//return finance_charts_call(quotename.toUpperCase(),'1m');
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
		(alert) ? draw_template(data) :
				  call_for_template($(this).attr('id'), data);
	});
	// Quote result menu
	$('#rselect li').on('click', function(){
		var quotename = $('#rquote').html();
		$('#rselect li').removeClass('active');
		$(this).addClass('active');
		if ($(this).html() == 'Candle chart') {
			finance_charts_call(quotename.toUpperCase(),'1m');
		} else {
			yql_table(quotename.toUpperCase(),'2014-12-01', '2014-12-25');
		}
	});
	var response = { "meta" :
 {
  "uri" :"/instrument/1.0/FB/chartdata;type=quote;range=1m/json/?chart=jQuery21306741342437453568_1426419505074&_=1426419505075",
  "ticker" : "fb",
  "Company-Name" : "Facebook, Inc.",
  "Exchange-Name" : "NMS",
  "unit" : "DAY",
  "timestamp" : "",
  "first-trade" : "20120518",
  "last-trade" : "20150313",
  "currency" : "USD",
  "previous_close_price" : 76.2300
 }
 ,
 "Date" : {"min" :20150213,"max" :20150313 }
 ,
 "labels" : [20150213,20150217,20150223,20150302,20150309 ]
 ,
 "ranges" : {"close" : {"min" :75.6000,"max" :81.2100 },"high" : {"min" :76.4800,"max" :81.9900 },"low" : {"min" :75.0800,"max" :81.0500 },"open" : {"min" :75.3000,"max" :81.2300 },"volume" : {"min" :16015600,"max" :45851200 } }
 ,
 "series" : [
 { "Date" :20150213,"close" :75.7400,"high" :76.4800,"low" :75.5000,"open" :76.4600,"volume" :18621900 } 
, { "Date" :20150217,"close" :75.6000,"high" :76.9100,"low" :75.0800,"open" :75.3000,"volume" :25254400 } 
, { "Date" :20150218,"close" :76.7100,"high" :76.9000,"low" :75.4500,"open" :75.9400,"volume" :22426400 } 
, { "Date" :20150219,"close" :79.4200,"high" :79.8400,"low" :76.9500,"open" :76.9900,"volume" :45851200 } 
, { "Date" :20150220,"close" :79.9000,"high" :80.3400,"low" :79.2000,"open" :79.5500,"volume" :36931700 } 
, { "Date" :20150223,"close" :78.8400,"high" :80.1900,"low" :78.3800,"open" :79.9600,"volume" :24139100 } 
, { "Date" :20150224,"close" :78.4500,"high" :79.4800,"low" :78.1000,"open" :78.5000,"volume" :18897100 } 
, { "Date" :20150225,"close" :79.5600,"high" :80.2000,"low" :78.5000,"open" :78.5000,"volume" :25593800 } 
, { "Date" :20150226,"close" :80.4100,"high" :81.3700,"low" :79.7200,"open" :79.8800,"volume" :31111900 } 
, { "Date" :20150227,"close" :78.9700,"high" :81.2300,"low" :78.6200,"open" :80.6800,"volume" :30635700 } 
, { "Date" :20150302,"close" :79.7500,"high" :79.8600,"low" :78.5200,"open" :79.0000,"volume" :21604400 } 
, { "Date" :20150303,"close" :79.6000,"high" :79.7000,"low" :78.5200,"open" :79.6100,"volume" :18567300 } 
, { "Date" :20150304,"close" :80.9000,"high" :81.1500,"low" :78.8500,"open" :79.3000,"volume" :28014500 } 
, { "Date" :20150305,"close" :81.2100,"high" :81.9900,"low" :81.0500,"open" :81.2300,"volume" :27773300 } 
, { "Date" :20150306,"close" :80.0100,"high" :81.3300,"low" :79.8300,"open" :80.9000,"volume" :24332500 } 
, { "Date" :20150309,"close" :79.4400,"high" :79.9100,"low" :78.6300,"open" :79.6800,"volume" :18890800 } 
, { "Date" :20150310,"close" :77.5500,"high" :79.2600,"low" :77.5500,"open" :78.5000,"volume" :22832300 } 
, { "Date" :20150311,"close" :77.5700,"high" :78.4300,"low" :77.2600,"open" :77.8000,"volume" :20119700 } 
, { "Date" :20150312,"close" :78.9300,"high" :79.0500,"low" :77.9100,"open" :78.1000,"volume" :16015600 } 
, { "Date" :20150313,"close" :78.0500,"high" :79.3800,"low" :77.6800,"open" :78.6000,"volume" :18457000 } 

]
};
	//finance_charts_json_callback( response );
};

// get started
$(document).ready(main);