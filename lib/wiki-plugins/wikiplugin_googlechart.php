<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_googlechart_info()
{

	return [
		'name' => tra('Google Analytics Chart'),
		'description' => tra('Draws charts from Google Analytics Data.'),
		'prefs' => ['wikiplugin_googlechart'],
		'iconname' => 'bookmark',
		'format' => 'html',
		'introduced' => 15,
		'validate' => 'all',
		'params' => [
			'credentials' => [
				'name' => tra('JSON File'),
				'description' => tra('Location of the (service account) credentials JSON file. Must be kept private and not web accessible. (Only needed on the first plugin on each page)'),
				'required' => false,
				'since' => '15.0',
				'filter' => 'text',
			],
			'query_ids' => [
				'name' => tra('Profle IDs'),
				'description' => tra('In the format ga:XXXXXXX where XXXXXXX the URL of the analytics admin.'),
				// see https://developers.google.com/analytics/devguides/reporting/core/v3/#user_reports or https://ga-dev-tools.appspot.com/query-explorer/ for more
				'since' => '15.0',
				'required' => true,
				'filter' => 'text',
			],
			'query' => [
				'name' => tra('Query String'),
				'description' => tr('Can be used instead of the parameters below. E.g. "metrics=ga:users&dimensions=ga:country", query can be generated here: %0', '<a href="https://ga-dev-tools.appspot.com/query-explorer">ga-dev-tools</a>'),
				'since' => '15.0',
				'size' => 60,
				'required' => false,
				'default' => '',
				'filter' => 'text',
			],
			'query_metrics' => [
				'name' => tra('Metrics'),
				'description' => tra('e.g. "ga:pageviews", default "ga:sessions,ga:users"'),
				'since' => '15.0',
				'required' => false,
				'default' => 'ga:sessions,ga:users',
				'filter' => 'text',
			],
			'query_start-date' => [
				'name' => tra('Start Date'),
				'description' => tra('default "30daysAgo"'),
				'since' => '15.0',
				'required' => false,
				'default' => '30daysAgo',
				'filter' => 'text',
			],
			'query_end-date' => [
				'name' => tra('End Date'),
				'description' => tra('default "yesterday"'),
				'since' => '15.0',
				'required' => false,
				'default' => 'yesterday',
				'filter' => 'text',
			],
			'query_dimensions' => [
				'name' => tra('Dimensions'),
				'description' => tra('default "ga:date"'),
				'since' => '15.0',
				'required' => false,
				'default' => 'ga:date',
				'filter' => 'text',
			],
			'query_segment' => [
				'name' => tra('Segment'),
				'description' => tra('default ""'),
				'since' => '15.0',
				'required' => false,
				'default' => '',
				'filter' => 'text',
			],
			'query_max-results' => [
				'name' => tra('Max Results'),
				'description' => tra('default 50'),
				'since' => '15.0',
				'required' => false,
				'default' => 50,
				'filter' => 'int',
			],
			'query_sort' => [
				'name' => tra('Sort'),
				'description' => tra('One of the metrics usually, e.g. "-ga:sessions" default ""'),
				'since' => '15.0',
				'required' => false,
				'default' => '',
				'filter' => 'text',
			],
			'chart_container' => [
				'name' => tra('Container'),
				'description' => tra('ID of a DIV to contain the chart (optional)'),
				'since' => '15.0',
				'required' => false,
				'filter' => 'text',
			],
			'chart_type' => [
				'name' => tra('Chart Type'),
				'description' => tra('Type of chart, e.g. LINE, PIE etc.'),
				'since' => '15.0',
				'required' => false,
				'filter' => 'word',
				'default' => 'LINE',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Line'), 'value' => 'LINE'],
					['text' => tra('Column'), 'value' => 'COLUMN'],
					['text' => tra('Bar'), 'value' => 'BAR'],
					['text' => tra('Pie'), 'value' => 'PIE'],
					['text' => tra('Table'), 'value' => 'TABLE'],
					['text' => tra('Geo'), 'value' => 'GEO'],
				],
			],
			'width' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Chart Width'),
				'description' => tr('In pixels or percentage. Default value is %0.', '<code>100%</code>'),
				'since' => '15.0',
				'default' => '100%',
				'filter' => 'text',
			],
			'height' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Chart Height'),
				'description' => tr('In pixels or percentage. Default value is %0.', '<code>300</code>'),
				'since' => '15.0',
				'default' => '300',
				'filter' => 'text',
			],
			'float' => [
				'required' => false,
				'name' => tra('Float Position'),
				'description' => tr('Set the alignment for the entire element. For elements with a width of less than
				100%, other elements will wrap around it unless the %0 parameter is appropriately set.', '<code>clear</code>'),
				'since' => '15.0',
				'filter' => 'alpha',
				'safe' => true,
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
		],
	];
}

function wikiplugin_googlechart($data, $params)
{

	static $id = 0;
	$unique = 'wpdialog_' . ++$id;
	$headerlib = TikiLib::lib('header');

	$defaults = [];
	$plugininfo = wikiplugin_googlechart_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	if ($id === 1 && (empty($params['credentials']) || ! is_readable($params['credentials']))) {
		return tra('googlechart: No credentials file.');
	}

	if (empty($params['query_ids'])) {
		return tra('googlechart: No query_ids supplied.');
	}

	if (empty($params['chart_container'])) {
		$params['chart_container'] = $unique;
	}

	if (is_numeric($params['width'])) {
		$params['width'] .= 'px';
	}

	if (is_numeric($params['height'])) {
		$params['height'] .= 'px';
	}

	if (! empty($params['float'])) {
		$params['float'] = ' float:' . $params['float'];
	}

	if (! empty($params['class'])) {
		$params['class'] = ' class="' . $params['class'] . '"';
	} else {
		$params['class'] = ' class="wp_googlechart"';
	}

	// to business, the magic word...
	$access_token = wikiplugin_googlechart_authenticate($params['credentials']);

	$js = '(function (w, d, s, g, js, fs) {
	g = w.gapi || (w.gapi = {});
	g.analytics = {
		q: [], ready: function (f) {
			this.q.push(f);
		}
	};
	js = d.createElement(s);
	fs = d.getElementsByTagName(s)[0];
	js.src = "https://apis.google.com/js/platform.js";
	fs.parentNode.insertBefore(js, fs);
	js.onload = function () {
		g.load("analytics");
	};
}(window, document, "script"));';

	$headerlib->add_js($js);

	$query = [];
	if (! empty($params['query'])) {
		parse_str($params['query'], $query);

		if (empty($query['ids'])) {
			$query['ids'] = $params['query_ids'];
		}
	}
	$queryp = [];
	foreach ($params as $key => $param) {
		if (strpos($key, 'query_') === 0) {
			$queryp[substr($key, strlen('query_'))] = $param;
		}
	}
	$query = array_merge($queryp, $query);
	$query = json_encode(array_filter($query));

	$chart = ['options' => ['width' => '100%']];

	foreach ($params as $key => $param) {
		if (strpos($key, 'chart_') === 0) {
			$chart[substr($key, strlen('query_'))] = $param;
		}
	}
	$chart = json_encode(array_filter($chart));

	$js = "
gapi.analytics.ready(function () {

	try {
		/**
		 * Authorize the user with an access token obtained server side.
		 */

		gapi.analytics.auth.authorize({
			'serverAuth': {
				'access_token': '$access_token'
			}
		});

	} catch (e) {
		console.log('Chart error: ' + e);
	}
});
";
	$headerlib->add_js($js, 15);	// auth can be shared

	$js = "
gapi.analytics.ready(function () {
	try {
		var dataChart1 = new gapi.analytics.googleCharts.DataChart({
			query: {$query},
			chart: {$chart}
		});

		dataChart1.execute();

	} catch (e) {
		console.log('Chart \"$unique\" error: ' + e.message);
	}
});
";

	$headerlib->add_js($js, 16);

	$return = '<div id="' . $params['chart_container'] . '"' . $params['class'] .
		' style="width:' . $params['width'] . ';height:' . $params['height'] . ';' . $params['float'] . '"></div>';

	return $return;
}

function wikiplugin_googlechart_authenticate($credentials_file)
{

	$client = new Google_Client();
	$token = isset($_SESSION['ga_access_token']) && $_SESSION['ga_access_token'] ? $_SESSION['ga_access_token'] : false;

	if (empty($token) || $token['created'] + 3600 < time()) {	// in v2 it will be $token['expires_in'] but hard coded to 3600 for v1 api

		$data = json_decode(file_get_contents($credentials_file));

		$cred = new Google_Auth_AssertionCredentials(
			$data->client_email,
			[Google_Service_Analytics::ANALYTICS_READONLY],
			$data->private_key
		);

		$client->setAssertionCredentials($cred);

		if ($client->getAuth()->isAccessTokenExpired()) {
			$client->getAuth()->refreshTokenWithAssertion($cred);
		}

		$client->setApplicationName("The Networked Planet Test App");
		$client->setAccessType('offline');

		$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

		$token = $client->getAccessToken();
		$token = json_decode($token, true);

		$_SESSION['ga_access_token'] = $token;
	}

	if ($token) {
		try {
			$client->setAccessToken(json_encode($_SESSION['ga_access_token']));
		} catch (Exception $e) {
			Feedback::error(tr('googlechart exception: %0', $e->getMessage()));

			return false;
		}

		return $token['access_token'];
	} else {    // no token

		return false;
	}
}
