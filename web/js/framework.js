if ( ! ('onhashchange' in window)) {
	var old_href = location.href;
	setInterval( function() {
		var new_href = location.href;
		if (old_href != new_href){
				old_href = new_href;
				on_hash_change.call(window, {type: 'hashchange', 'newURL' : new_href, 'oldURL': old_href});
		}
	}, 100 );
	
} else if ( window.addEventListener ) {
	window.addEventListener('hashchange', on_hash_change, false);
	
} else if ( window.attachEvent ) {
	window.attachEvent('onhashchange', on_hash_change);
}

function get_cookie(name){
	if (document.cookie.length > 0){
		start = document.cookie.indexOf(name + '=');
		if (start != -1){
			start += name.length + 1;
			end = document.cookie.indexOf(';', start);
			if (end == -1) end = document.cookie.length;
			return unescape(document.cookie.substring(start, end));
		}
	}
	return '';
}

function randomize(url) {
	//if (url.indexOf('?') == -1) url += '?';
	//url += '&seed=' + Math.random();
	return url;
}

function hash_to_url(hash) {
	if (hash[0] == '#') hash = hash.substr(1);
	url = "index.php/" + hash;
	return url;
}

function set_page_content(selector, url, success) {
	url = randomize(url);
	$.ajax({
		type: "GET",
		url: url,
		success: function(data){
			$(selector).hide();
			$(selector).html(data);
			$(selector).fadeIn(250);
			if (success != void 0) success();
		},
		error: function(xhr, statusText, error){
			$(selector).html('<div class="alert"><strong>Error: ' + ' ' + error + '</strong></div>');
		}
	});
}

function access_page(hash, success){
	url = randomize(hash_to_url(hash));
	refresh = arguments.length == 3 && arguments[2] == false ? false : true;
	if (refresh){
		$.get(url, function(){
			set_page_content('#page_content', hash_to_url(window.location.hash), success);
		});
	}else{
		$.get(url, success);
	}
}

function load_page(url) {
	window.location.hash = url;
	return false;
}

function refresh_page() {
	if (typeof refresh_flag != 'undefined'){
		clearTimeout(refresh_flag);
		delete refresh_flag;
	}
 	set_page_content('#page_content', hash_to_url(window.location.hash));
}

function on_hash_change() {
	if (window.preventHashchange) {
		window.preventHashchange = false;
		return;
	}
	
	if (typeof refresh_flag != 'undefined') {
		clearTimeout(refresh_flag);
		delete refresh_flag;
	}
	
	set_page_content('#page_content', hash_to_url(window.location.hash));
}

function init_framework() {
	window.preventHashchange = false;
	
	if (window.location.hash != '') set_page_content('#page_content', hash_to_url(window.location.hash));
	else load_page('main/home');
	var priviledge = get_cookie('priviledge');
	if (priviledge == 'admin') $('.nav_admin').attr({style:"display:block"});
	else $('.nav_admin').attr({style:"display:none"});
}

function load_userinfo() {
	set_page_content('#userinfo', "index.php/main/userinfo");
}

function login_submit() {
	$('#login_field').modal('hide');
	$('#login_form').ajaxSubmit({
		success: function login_success(responseText, stautsText){
			if (responseText == 'success'){
				load_userinfo();
				set_page_content('#page_content', hash_to_url(window.location.hash));
				var priviledge = get_cookie('priviledge');
				if (priviledge == 'admin') $('.nav_admin').attr({style:"display:block"});
				else $('.nav_admin').attr({style:"display:none"});
			} else $('#page_content').html(responseText);
		}
	});
	return false;
}

function register_submit() {
	$('#register_field').modal('hide');
	$('#register_form').ajaxSubmit({
		success: function(responseText, statusText){
			if (responseText == 'success') load_page('main/home');
			else $('#page_content').html(responseText);
		}
	});
	return false;
}

$(document).ready( function() {
	$('.case').click(function() {
		var attr = "." + $(this).attr("id");
		$(this).siblings(attr).slideToggle(5);
	}),
	
	$('#navigation li a').click( function() {
		$('#navigation li').removeClass('active');
		$(this).parent().addClass('active');
	}),
	
	$('#logout').live('click', function() {
		access_page('main/logout', load_userinfo);
	})
})

function initialize() {
    // Radialize the colors
	Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
	    return {
	        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
	        stops: [
	            [0, color],
	            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
	        ]
	    };
	});
}

function render_pie(selector, title, data) {
	// Build the chart
	$(selector).highcharts({
		chart: {
			plotBorderWidth: null,
			backgroundColor: 'transparent'
		},
		title: {
			text: title
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage}%</b>',
			percentageDecimals: 2
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage) +' %';
					}
				}
			}
		},
		series: data
	});
}

function render_column(selector, title, data) {
	$(selector).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'transparent'
		},
		title: { text: title },
		xAxis: { categories: ['Categories'] },
		yAxis: {
			min: 0,
			title: { text: 'Count' }
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
		},
		series: data
	});
}
