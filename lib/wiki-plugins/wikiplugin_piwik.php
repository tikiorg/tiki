<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_piwik.php 56291 2015-09-22 02:32:43Z bsfez $

function wikiplugin_piwik_info()
{
	return array(
		'name' => tra('Piwik'),
		'documentation' => 'PluginPiwik',
		'description' => tr('Embed a Piwik preformatted report (widget module) - Piwik Analytics is required.
                            To use this plugin you have to grant in your Piwik view permission to anonymous for the selected "Site Id" or to add a token authentification parameter.'),
		'prefs' => array('wikiplugin_piwik'),
		'iconname' => 'chart',
		'introduced' => 15,
		'tags' => array('basic'),
		'format' => 'html',
		'params' => array(
			'piwikserverurl' => array(
				'required' => false,
				'name' => tra('Piwik server url'),
				'description' => tr('The url to your Piwik Server, where data for the report are collected and available.') . ' <code>http(s)://yourpiwik.tld/index.php?</code> ' . '<br />'
					. tr('In your Piwik, your selected site (Site Id) MUST have view permission for anonymous OR you can insert in your Piwik server url a token authentification parameter.') . '<br />'
					. '<code>http(s)://yourpiwik.tld/index.php&token_auth=yourtokencode</code> ' . tr('Important : token_auth is visible in the html code and must be used in private page accessible to trusted users.'),
				'since' => '15',
				'default' => '',
			),

			'idSite' => array(
				'required' => false,
				'name' => tra('Site Id'),
				'description' => tr('The ID of your website in Piwik. To be improved.'),
				'since' => '15',
				'filter' => 'digits',
				'default' => '',
			),

			'moduleToWidgetize' => array(
				'required' => false,
				'name' => tra('Module and Action To Widgetize'),
				'description' => tr('Piwik widget module to be used (as described in the widget section of your Piwik server) followed by the actionToWidgetize parameter separated by a comma.'),
				'since' => '15',
				'default' => 'VisitsSummary,getEvolutionGraph',
				'options' => array(
					array('text' => tra('Actions - Pages'), 'value' => 'Actions,getPageUrls'),
					array('text' => tra('Actions - Entry pages'), 'value' => 'Actions,getEntryPageUrls'),
					array('text' => tra('Actions - Exit pages'), 'value' => 'Actions,getExitPageUrls'),
					array('text' => tra('Actions - Outlinks'), 'value' => 'Actions,getOutlinks'),
					array('text' => tra('Dashboard'), 'value' => 'Dashboard,index'),
					array('text' => tra('Live - Visitor in real time'), 'value' => 'Live,widget'),
					array('text' => tra('Live - Simple Last Visit Count'), 'value' => 'Live,getSimpleLastVisitCount'),
					array('text' => tra('Live - Visitor Profile Popup'), 'value' => 'Live,getVisitorProfilePopup'),
					array('text' => tra('Referrers - All referrers'), 'value' => 'Referrers,getAll'),
					array('text' => tra('Referrers - Search engines'), 'value' => 'Referrers,getSearchEngines'),
					array('text' => tra('Resolution - Screen Resolution'), 'value' => 'Resolution,getResolution'),
					array('text' => tra('SEO - SEO Ranking (slow)'), 'value' => 'SEO,getRank'),
					array('text' => tra('User Country Map - RealTime Map'), 'value' => 'UserCountryMap,realtimeMap'),
					array('text' => tra('User Country Map - Visitor Map'), 'value' => 'UserCountryMap,visitorMap'),
					array('text' => tra('User Country Map - Country'), 'value' => 'UserCountryMap,getCountry'),
					array('text' => tra('User Country Map - Continent'), 'value' => 'UserCountryMap,getContinent'),
					array('text' => tra('User Language - Language'), 'value' => 'UserLanguage,getLanguage'),
					array('text' => tra('User Language - Language Code'), 'value' => 'UserLanguage,getLanguageCode'),
					array('text' => tra('Visits Time - By day of the week'), 'value' => 'VisitTime,getByDayOfWeek'),
					array('text' => tra('Visits Time - Visit Information Per Local Time'), 'value' => 'VisitTime,getVisitInformationPerLocalTime'),
					array('text' => tra('Visits Time - Visit Information Per Server Time'), 'value' => 'VisitTime,getVisitInformationPerServerTime'),
					array('text' => tra('Visits Summary - by day of the week'), 'value' => 'VisitTime,getByDayOfWeek'),
					array('text' => tra('Visits Summary - over time'), 'value' => 'VisitsSummary,getEvolutionGraph'),
					array('text' => tra('Visits Summary - overview with graph'), 'value' => 'VisitsSummary,index'),
					array('text' => tra('Visitors - Visitor map'), 'value' => 'UserCountryMap,visitorMap'),
					array('text' => tra('Visitors - Pages per visit'), 'value' => 'VisitorInterest,getNumberOfVisitsPerVisitDuration'),
					array('text' => tra('Visitors - Frequency overview'), 'value' => 'VisitFrequency,getSparklines'),
                    array('text' => tra('Visitors - Returning visits over time'), 'value' => 'VisitFrequency,getEvolutionGraph'),
                    array('text' => tra('Visitor Devices - Device type'), 'value' => 'DevicesDetection,getType'),
					array('text' => tra('Visitor Devices - browser'), 'value' => 'DevicesDetection,getBrowsers'),
					array('text' => tra('Visitor Devices - Browser Versions'), 'value' => 'DevicesDetection,getBrowserVersions'),
					array('text' => tra('Visitor Devices - OS Families'), 'value' => 'DevicesDetection,getOsFamilies'),
					array('text' => tra('Visitor Devices - OS Versions'), 'value' => 'DevicesDetection,getOsVersions'),
					array('text' => tra('Visitor Interest - Number Of Visits Per Visit Duration'), 'value' => 'VisitorInterest,getNumberOfVisitsPerVisitDuration'),
					array('text' => tra('Visitor Interest - Number Of Visits Per Page'), 'value' => 'VisitorInterest,getNumberOfVisitsPerPage'),
					array('text' => tra('Visitor Interest - Number Of Visits By Visit Count'), 'value' => 'VisitorInterest,getNumberOfVisitsByVisitCount'),
					array('text' => tra('Visitor Interest - Number Of Visits By Days Since Last'), 'value' => 'VisitorInterest,getNumberOfVisitsByDaysSinceLast'),
					array('text' => tra('Visitor Setting - Screen resolution'), 'value' => 'Resolution,getResolution'),
				),
			),

			'period' => array(
				'required' => false,
				'name' => tra('Statistics period'),
				'description' => tr('Data display duration. If range is selected you must enter the start and end the date.'),
				'since' => '15',
				'default' => 'day',
				'options' => array(
					array('text' => tra('Day'), 'value' => 'day'),
					array('text' => tra('Week'), 'value' => 'week'),
					array('text' => tra('Month'), 'value' => 'month'),
					array('text' => tra('Year'), 'value' => 'year'),
					array('text' => tra('Range'), 'value' => 'range'),
				),
			),

			'date' => array(
				'required' => false,
				'name' => tra('Date or Start date'),
				'description' => tr('Enter date or start date for the data to be displayed (yesterday by default). Possible values are: today, yesterday, and yyyy-mm-dd.'),
				'since' => '15',
				'default' => 'yesterday',
			),

			'enddate' => array(
				'required' => false,
				'name' => tra('End date'),
				'description' => tr('Enter end date (format yyyy-mm-dd) for the data to be displayed (only if range period is selected).'),
				'since' => '15',
				'default' => '',
			),

			'_width' => array(
				'required' => false,
				'name' => tra('Module width'),
				'description' => tr('Optional, width of the module in px or % (100% by default).'),
				'since' => '15',
				'default' => '100%',
			),

			'_height' => array(
				'required' => false,
				'name' => tra('Module height'),
				'description' => tr('Optional, height of the module in px.'), // Would be nice to have this as auto - checking if number of rows is applicable.
				'since' => '15',
				'default' => '265',
			),

			'_scrolling' => array(
				'required' => false,
				'name' => tra('Iframe Scrolling'),
				'description' => tr('Optional, scrolling of the iframe that contain the module (no by default).'),
				'since' => '15',
				'default' => 'no',
				'options' => array(
					array('text' => tra('No'), 'value' => 'no'),
					array('text' => tra('Yes'), 'value' => 'yes'),
				),
			),
		),
	);
}

function wikiplugin_piwik($data, $params)
{
	global $prefs;

	$plugininfo = wikiplugin_piwik_info();
	$default = array();
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

	foreach($params as $key => $value) {
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
