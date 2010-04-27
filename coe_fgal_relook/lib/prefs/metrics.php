<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_metrics_list() {
	return array(
		'metrics_pastresults' => array(
			'name' => tra('Show past metrics results'),
			'description' => tra('In the metrics dashboard, identify if the previously collected metrics should be displayed.'),
			'type' => 'flag',
		),
		'metrics_pastresults_count' => array(
			'name' => tra('Past metrics count'),
			'description' => tra('Amount of past results to display.'),
			'type' => 'text',
			'size' => 4,
			'filter' => 'digits',
		),
		'metrics_trend_novalue' => array(
			'name' => tra('No metric trend value'),
			'description' => tra('Value to display when no trend is available for the metric.'),
			'type' => 'text',
			'size' => 10,
		),
		'metrics_trend_prefix' => array(
			'name' => tra('Metric trend prefix'),
			'description' => tra('Portion of text to display before the metric trend value.'),
			'type' => 'text',
			'size' => 10,
		),
		'metrics_trend_suffix' => array(
			'name' => tra('Metric trend suffix'),
			'description' => tra('Portion of text to display after the metric trend value.'),
			'type' => 'text',
			'size' => 10,
		),
		'metrics_metric_name_length' => array(
			'name' => tra('Metric name length'),
			'type' => 'text',
			'size' => 4,
			'filter' => 'digits',
		),
		'metrics_tab_name_length' => array(
			'name' => tra('Metric tab name length'),
			'type' => 'text',
			'size' => 4,
			'filter' => 'digits',
		),
		'metrics_cache_output' => array(
			'name' => tra('Cache metrics output'),
			'description' => tra('Most metrics are expensive to generate. Because they tend to evolve slowly, some delay between update and generation can be tolerated. Disabling cache is not recommended.'),
			'type' => 'flag',
			'dependencies' => array(
				'memcache_enabled',
				'memcache_servers',
			),
		),
	);
}

