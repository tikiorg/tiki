<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id:  $

function wikiplugin_benchmark_info()
{
	return array(
		'name' => tra('Benchmark'),
		//'documentation' => tra('PluginTransclude'),
		'description' => tra('Performance test wiki content. Used by tiki developers to optimize plugins. '),
		'prefs' => array('wikiplugin_benchmark'),
		//'extraparams' => true,
		'defaultfilter' => 'text',
		//'iconname' => 'copy',
		'introduced' => 17,
		'format' => 'html',
		'params' => array(
			'times' => array(
				'required' => false,
				'name' => tra('Iteration Quantity'),
				'description' => tra('The number of iterations to process.'),
				'since' => '17.0',
				'default' => '1000',
				'filter' => 'alpha',
				//'profile_reference' => 'wiki_page',
			),
			'details' => array(
				'required' => false,
				'name' => tra('Each Iteration Details'),
				'description' => tra('Provides time and memory of each iteration.'),
				'since' => '17.0',
				'default' => 'false',
				'filter' => 'alpha',
				//'profile_reference' => 'wiki_page',
			),
		),
	);
}


function wikiplugin_benchmark( $data, $params ){
	global $smarty;
	$parserlib =TikiLib::lib('parser');

	if (!isset($params['times']))
		$params['times'] = 100;

	// Overhead Benchmark
	$parserlib->parse_data_plugin('a');
	$begin = microtime(true);
	for ($times = 0; $times < 10; $times++)
		$parserlib->parse_data_plugin('a');
	$end = microtime(true);

	$smarty->assign('overTime',round((($end - $begin)/10),4));

	if (isset($params['details'])){
		$iterations = array();
		// Complete iterations benchmark
		$begin = microtime(true);
		$memBegin = memory_get_usage(true);
		for ($times = 0; $times < ($params['times']-2); $times++) {
			$timeBeginI = microtime(true);
			$memBeginI = memory_get_usage(true);
			$parserlib->parse_data_plugin($data);
			$memEndI = memory_get_usage(true);
			$timeEndI = microtime(true);
			$iterations['time'][] = round(($timeEndI - $timeBeginI),4);
			$iterations['mem'][] = $memEndI - $memBeginI;
		}
		$end = microtime(true);
		$memEnd = memory_get_usage(true);

		$smarty->assign('iterations',$iterations);
		$smarty->assign('times',$params['times']);
		$smarty->assign('time',round(($end - $begin)/60,4));
		$smarty->assign('memory',$memEnd - $memBegin);
		return $smarty->fetch('lib/wiki-plugins/wikiplugin_benchmark.tpl').$parserlib->parse_data_plugin($data);


	}

	// if were not disclosing details, provide some

	// Complete iterations benchmark
	$memBegin = memory_get_usage(true);
	$parserlib->parse_data_plugin($data);
	$memEnd = memory_get_usage(true);

	$smarty->assign('firstMemory',$memEnd - $memBegin);

	// Complete iterations benchmark
	$memBegin = memory_get_usage(true);
	$parserlib->parse_data_plugin($data);
	$memEnd = memory_get_usage(true);

	$smarty->assign('secondMemory',$memEnd - $memBegin);

	// Complete iterations benchmark
	$begin = microtime(true);
	$memBegin = memory_get_usage(true);
	for ($times = 0; $times < $params['times']; $times++) {
		$parserlib->parse_data_plugin($data);
	}
	$end = microtime(true);
	$memEnd = memory_get_usage(true);

	$smarty->assign('times',$params['times']);
	$smarty->assign('time',round(($end - $begin)/60,4));
	$smarty->assign('memory',$memEnd - $memBegin);
	return $smarty->fetch('lib/wiki-plugins/wikiplugin_benchmark.tpl').$parserlib->parse_data_plugin($data);
}
