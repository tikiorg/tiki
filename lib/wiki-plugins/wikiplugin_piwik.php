<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_piwik_info()
{
	return [
		'name' => tra('Piwik'),
		'documentation' => 'PluginPiwik',
		'description' => tr('Embed a Piwik preformatted report (widget module) - Piwik Analytics is required.
                            To use this plugin you have to grant in your Piwik view permission to anonymous for the selected "Site Id" or to add a token authentification parameter.'),
		'prefs' => ['wikiplugin_piwik'],
		'iconname' => 'chart',
		'introduced' => 15,
		'tags' => ['basic'],
		'format' => 'html',
		'params' => [
			'piwikserverurl' => [
				'required' => false,
				'name' => tra('Piwik server url'),
				'description' => tr('The url to your Piwik Server, where data for the report are collected and available.') . ' <code>http(s)://yourpiwik.tld/index.php?</code> ' . '<br />'
					. tr('In your Piwik, your selected site (Site Id) MUST have view permission for anonymous OR you can insert in your Piwik server url a token authentification parameter.') . '<br />'
					. '<code>http(s)://yourpiwik.tld/index.php&token_auth=yourtokencode</code> ' . tr('Important : token_auth is visible in the html code and must be used in private page accessible to trusted users.'),
				'since' => '15',
				'default' => '',
			],

			'idSite' => [
				'required' => false,
				'name' => tra('Site Id'),
				'description' => tr('The ID of your website in Piwik. To be improved.'),
				'since' => '15',
				'filter' => 'digits',
				'default' => '',
			],

			'moduleToWidgetize' => [
				'required' => false,
				'name' => tra('Module and Action To Widgetize'),
				'description' => tr('Piwik widget module to be used (as described in the widget section of your Piwik server) followed by the actionToWidgetize parameter separated by a comma.'),
				'since' => '15',
				'default' => 'VisitsSummary,getEvolutionGraph',
				'options' => [
					['text' => tra('Actions - Pages'), 'value' => 'Actions,getPageUrls'],
					['text' => tra('Actions - Entry pages'), 'value' => 'Actions,getEntryPageUrls'],
					['text' => tra('Actions - Exit pages'), 'value' => 'Actions,getExitPageUrls'],
					['text' => tra('Actions - Outlinks'), 'value' => 'Actions,getOutlinks'],
					['text' => tra('Dashboard'), 'value' => 'Dashboard,index'],
					['text' => tra('Live - Visitor in real time'), 'value' => 'Live,widget'],
					['text' => tra('Live - Simple Last Visit Count'), 'value' => 'Live,getSimpleLastVisitCount'],
					['text' => tra('Live - Visitor Profile Popup'), 'value' => 'Live,getVisitorProfilePopup'],
					['text' => tra('Referrers - All referrers'), 'value' => 'Referrers,getAll'],
					['text' => tra('Referrers - Search engines'), 'value' => 'Referrers,getSearchEngines'],
					['text' => tra('Resolution - Screen Resolution'), 'value' => 'Resolution,getResolution'],
					['text' => tra('SEO - SEO Ranking (slow)'), 'value' => 'SEO,getRank'],
					['text' => tra('User Country Map - RealTime Map'), 'value' => 'UserCountryMap,realtimeMap'],
					['text' => tra('User Country Map - Visitor Map'), 'value' => 'UserCountryMap,visitorMap'],
					['text' => tra('User Country Map - Country'), 'value' => 'UserCountryMap,getCountry'],
					['text' => tra('User Country Map - Continent'), 'value' => 'UserCountryMap,getContinent'],
					['text' => tra('User Language - Language'), 'value' => 'UserLanguage,getLanguage'],
					['text' => tra('User Language - Language Code'), 'value' => 'UserLanguage,getLanguageCode'],
					['text' => tra('Visits Time - By day of the week'), 'value' => 'VisitTime,getByDayOfWeek'],
					['text' => tra('Visits Time - Visit Information Per Local Time'), 'value' => 'VisitTime,getVisitInformationPerLocalTime'],
					['text' => tra('Visits Time - Visit Information Per Server Time'), 'value' => 'VisitTime,getVisitInformationPerServerTime'],
					['text' => tra('Visits Summary - by day of the week'), 'value' => 'VisitTime,getByDayOfWeek'],
					['text' => tra('Visits Summary - over time'), 'value' => 'VisitsSummary,getEvolutionGraph'],
					['text' => tra('Visits Summary - overview with graph'), 'value' => 'VisitsSummary,index'],
					['text' => tra('Visitors - Visitor map'), 'value' => 'UserCountryMap,visitorMap'],
					['text' => tra('Visitors - Pages per visit'), 'value' => 'VisitorInterest,getNumberOfVisitsPerVisitDuration'],
					['text' => tra('Visitors - Frequency overview'), 'value' => 'VisitFrequency,getSparklines'],
					['text' => tra('Visitors - Returning visits over time'), 'value' => 'VisitFrequency,getEvolutionGraph'],
					['text' => tra('Visitor Devices - Device type'), 'value' => 'DevicesDetection,getType'],
					['text' => tra('Visitor Devices - browser'), 'value' => 'DevicesDetection,getBrowsers'],
					['text' => tra('Visitor Devices - Browser Versions'), 'value' => 'DevicesDetection,getBrowserVersions'],
					['text' => tra('Visitor Devices - OS Families'), 'value' => 'DevicesDetection,getOsFamilies'],
					['text' => tra('Visitor Devices - OS Versions'), 'value' => 'DevicesDetection,getOsVersions'],
					['text' => tra('Visitor Interest - Number Of Visits Per Visit Duration'), 'value' => 'VisitorInterest,getNumberOfVisitsPerVisitDuration'],
					['text' => tra('Visitor Interest - Number Of Visits Per Page'), 'value' => 'VisitorInterest,getNumberOfVisitsPerPage'],
					['text' => tra('Visitor Interest - Number Of Visits By Visit Count'), 'value' => 'VisitorInterest,getNumberOfVisitsByVisitCount'],
					['text' => tra('Visitor Interest - Number Of Visits By Days Since Last'), 'value' => 'VisitorInterest,getNumberOfVisitsByDaysSinceLast'],
					['text' => tra('Visitor Setting - Screen resolution'), 'value' => 'Resolution,getResolution'],
				],
			],

			'period' => [
				'required' => false,
				'name' => tra('Statistics period'),
				'description' => tr('Data display duration. If range is selected you must enter the start and end the date.'),
				'since' => '15',
				'default' => 'day',
				'options' => [
					['text' => tra('Day'), 'value' => 'day'],
					['text' => tra('Week'), 'value' => 'week'],
					['text' => tra('Month'), 'value' => 'month'],
					['text' => tra('Year'), 'value' => 'year'],
					['text' => tra('Range'), 'value' => 'range'],
				],
			],

			'date' => [
				'required' => false,
				'name' => tra('Date or Start date'),
				'description' => tr('Enter date or start date for the data to be displayed (yesterday by default). Possible values are: today, yesterday, and yyyy-mm-dd.'),
				'since' => '15',
				'default' => 'yesterday',
			],

			'enddate' => [
				'required' => false,
				'name' => tra('End date'),
				'description' => tr('Enter end date (format yyyy-mm-dd) for the data to be displayed (only if range period is selected).'),
				'since' => '15',
				'default' => '',
			],

			'_width' => [
				'required' => false,
				'name' => tra('Module width'),
				'description' => tr('Optional, width of the module in px or % (100% by default).'),
				'since' => '15',
				'default' => '100%',
			],

			'_height' => [
				'required' => false,
				'name' => tra('Module height'),
				'description' => tr('Optional, height of the module in px.'), // Would be nice to have this as auto - checking if number of rows is applicable.
				'since' => '15',
				'default' => '265',
			],

			'_scrolling' => [
				'required' => false,
				'name' => tra('Iframe Scrolling'),
				'description' => tr('Optional, scrolling of the iframe that contain the module (no by default).'),
				'since' => '15',
				'default' => 'no',
				'options' => [
					['text' => tra('No'), 'value' => 'no'],
					['text' => tra('Yes'), 'value' => 'yes'],
				],
			],
		],
	];
}

function wikiplugin_piwik($data, $params)
{
	global $prefs;

	$plugininfo = wikiplugin_piwik_info();
	$default = [];
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);

	if (empty($params['piwikserverurl'])) {
		$params['piwikserverurl'] = $prefs['site_piwik_analytics_server_url'];
	}

	if (empty($params['piwikserverurl'])) {
		return tra('Plugin Piwik error:') . ' ' . tra('Piwik server url is required.');
	}

	if (empty($params['idSite'])) {
		$params['idSite'] = $prefs['site_piwik_site_id'];
	}

	if (empty($params['idSite'])) {
		return tra('Plugin Piwik error:') . ' ' . tra('Site Id is required.');
	}

	if (empty($params['moduleToWidgetize'])) {
		return tra('Plugin Piwik error:') . ' ' . tra('moduleToWidgetize is required.');
	} else {
		$arr = explode(',', $params['moduleToWidgetize']);

		$params['moduleToWidgetize'] = $arr[0];
		$params['actionToWidgetize'] = $arr[1];
	}

	if ($params['period'] === 'range') {
		if ($params['enddate']) {
			$params['date'] .= ',' . $params['enddate'];
			unset($params['enddate']);
		} else {
			return tra('Plugin Piwik error:') . ' ' . tra('Period set to range but no end date provided.');
		}
	}

	// grab out the params that aren't going to be part of the url, they will be iframe attributes and start with an underscore
	$attributes = '';

	foreach ($params as $key => $value) {
		if (strpos($key, '_') === 0) {
			if ($value) {
				$attributes .= ' ' . substr($key, 1) . '="' . $value . '"';
			}
			unset($params[$key]);
		}
	}

	// parse the main url param and unset it
	$url_parts = parse_url($params['piwikserverurl']);
	unset($params['piwikserverurl']);

	// add the fixed query params
	$params['module'] = 'Widgetize';
	$params['action'] = 'iframe';
	$params['disableLink'] = 1;
	$params['widget=1'] = 1;

	// parse the query part of the url into an array
	parse_str($url_parts['query'], $query);

	// merge the params and the fixed ones
	$query = array_merge($params, $query);
	// convert back to a string and replace in the parts array
	$url_parts['query'] = http_build_query($query, '', '&amp;');

	$url  = TikiLib::unparse_url($url_parts);

	$iframe = ('<iframe src="' . $url . '" ' . $attributes . ' frameborder="0"></iframe>');

	return $iframe;
}
