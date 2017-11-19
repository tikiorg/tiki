<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_benchmark_info()
{
	return [
		'name' => tra('Benchmark'),
		//'documentation' => tra('PluginTransclude'),
		'description' => tra('Performance test wiki content. Used by tiki developers to optimize plugins. '),
		'prefs' => ['wikiplugin_benchmark'],
		//'extraparams' => true,
		'defaultfilter' => 'text',
		//'iconname' => 'copy',
		'introduced' => 17,
		'format' => 'html',
		'tags' => [ 'advanced' ],
		'params' => [
			'times' => [
				'required' => false,
				'name' => tra('Iteration Quantity'),
				'description' => tra('The number of iterations to process.'),
				'since' => '17.0',
				'default' => '1000',
				'filter' => 'alpha',
				//'profile_reference' => 'wiki_page',
			],
			'details' => [
				'required' => false,
				'name' => tra('Each Iteration Details'),
				'description' => tra('Provides time and memory of each iteration.'),
				'since' => '17.0',
				'default' => 'true',
				'filter' => 'alpha',
				//'profile_reference' => 'wiki_page',
			],
		],
	];
}


function wikiplugin_benchmark($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	$parserlib = TikiLib::lib('parser');

	if (! isset($params['times'])) {
		$params['times'] = 100;
	}

	if (isset($params['details']) && ($params['details'] == ''||$params['details'] == 'false')) {
		// if were not disclosing details

		$iterations = [];
		// Complete iterations benchmark
		$begin = microtime(true);
		$memBeginReal = memory_get_usage(true);
		$memBegin = memory_get_usage();
		for ($times = 0; $times < ($params['times']); $times++) {
			$parserlib->parse_data_plugin($data);
		}
		$end = microtime(true);
		$memEnd = memory_get_usage();
		$memEndReal = memory_get_usage(true);

		$smarty->assign('iterations', $iterations);
		$smarty->assign('times', $params['times']);
		$smarty->assign('time', round(($end - $begin) / 60, 4));
		$smarty->assign('memory', $memEnd - $memBegin);
		$smarty->assign('memoryReal', $memEndReal - $memBeginReal);
		return $smarty->fetch('templates/wiki-plugins/wikiplugin_benchmark.tpl') . $parserlib->parse_data_plugin($data);
	}

	// Complete iterations benchmark

	$iterations = [];
	$begin = microtime(true);
	$memBeginReal = memory_get_usage(true);
	$memBegin = memory_get_usage();
	for ($times = 0; $times < ($params['times']); $times++) {
		$timeBeginI = microtime(true);
		$memBeginIReal = memory_get_usage(true);
		$memBeginI = memory_get_usage();
		$parserlib->parse_data_plugin($data);
		$memEndI = memory_get_usage();
		$memEndIReal = memory_get_usage(true);
		$timeEndI = microtime(true);
		$iterations['time'][] = round(($timeEndI - $timeBeginI), 4);
		$iterations['mem'][] = $memEndI - $memBeginI;
		$iterations['memR'][] = $memEndIReal - $memBeginIReal;
	}
	$end = microtime(true);
	$memEnd = memory_get_usage();
	$memEndReal = memory_get_usage(true);

	$smarty->assign('iterations', $iterations);
	$smarty->assign('times', $params['times']);
	$smarty->assign('time', round(($end - $begin), 4));
	$smarty->assign('timeMicro', round(($end - $begin) * 60, 4));
	$smarty->assign('memory', $memEnd - $memBegin);
	$smarty->assign('memoryReal', $memEndReal - $memBeginReal);
	$smarty->assign('timeA', round(array_sum($iterations['time']) / $params['times'] / 60, 4));
	$smarty->assign('timeAMicro', round(array_sum($iterations['time']) / $params['times'], 4));
	$smarty->assign('memoryA', round(array_sum($iterations['mem']) / $params['times'], 0));
	$smarty->assign('memoryRealA', array_sum($iterations['memR']) / $params['times']);
	return $smarty->fetch('templates/wiki-plugins/wikiplugin_benchmark.tpl') . $parserlib->parse_data_plugin($data);
}
