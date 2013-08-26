<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (isset($_REQUEST['performance'])) {
	check_ticket('admin-inc-performance');
}

ask_ticket('admin-inc-performance');

$opcode_cache = null;
$stat_flag = null;
$opcode_stats = array(
	'warning_check' => false,
	'warning_fresh' => false,
	'warning_ratio' => false,
	'warning_starve' => false,
	'warning_low' => false,
	'warning_xcache_blocked' => false,
);

if ( function_exists('apc_sma_info') && ini_get('apc.enabled') ) {

	if ( $_REQUEST['apc_clear']) {
		check_ticket('admin-inc-performance');
		apc_clear_cache();
		apc_clear_cache('user');
		apc_clear_cache('opcode');
	}

	$opcode_cache = 'APC';

	$sma = apc_sma_info();
	$mem_total = $sma['num_seg'] * $sma['seg_size'];

	$cache = apc_cache_info(null, true);
	$hit_total = $cache['num_hits'] + $cache['num_misses'];
	if (!$hit_total) {	// cheat for chart after cache clear
		$hit_total = 1;
		$cache['num_misses'] = 1;
	}

	$stat_flag = 'apc.stat';
	$opcode_stats = array(
		'memory_used' => ( $mem_total - $sma['avail_mem'] ) / $mem_total,
		'memory_avail' => $sma['avail_mem'] / $mem_total,
		'memory_total' => $mem_total,
		'hit_hit' => $cache['num_hits'] / $hit_total,
		'hit_miss' => $cache['num_misses'] / $hit_total,
		'hit_total' => $hit_total,
		'type' => 'apc',
	);
} elseif ( function_exists('xcache_info') && ( ini_get('xcache.cacher') == '1' || ini_get('xcache.cacher') == 'On' ) ) {
	$opcode_cache = 'XCache';

	if ( ini_get('xcache.admin.enable_auth') == '1' || ini_get('xcache.admin.enable_auth') == 'On' ) {
		$opcode_stats['warning_xcache_blocked'] = true;
	} else {
		$stat_flag = 'xcache.stat';
		$opcode_stats = array(
			'memory_used' => 0,
			'memory_avail' => 0,
			'memory_total' => 0,
			'hit_hit' => 0,
			'hit_miss' => 0,
			'hit_total' => 0,
			'type' => 'xcache',
		);

		foreach (range(0, xcache_count(XC_TYPE_PHP) - 1) as $index) {
			$info = xcache_info(XC_TYPE_PHP, $index);

			$opcode_stats['hit_hit'] += $info['hits'];
			$opcode_stats['hit_miss'] += $info['misses'];
			$opcode_stats['hit_total'] += $info['hits'] + $info['misses'];

			$opcode_stats['memory_used'] += $info['size'] - $info['avail'];
			$opcode_stats['memory_avail'] += $info['avail'];
			$opcode_stats['memory_total'] += $info['size'];
		}

		$opcode_stats['memory_used'] /= $opcode_stats['memory_total'];
		$opcode_stats['memory_avail'] /= $opcode_stats['memory_total'];
		$opcode_stats['hit_hit'] /= $opcode_stats['hit_total'];
		$opcode_stats['hit_miss'] /= $opcode_stats['hit_total'];
	}
} elseif ( function_exists('wincache_ocache_fileinfo') && ( ini_get('wincache.ocenabled') == '1') ) {
	$opcode_cache = 'WinCache';

	$stat_flag = 'wincache.ocenabled';
	$opcode_stats = array(
		'memory_used' => 0,
		'memory_avail' => 0,
		'memory_total' => 0,
		'hit_hit' => 0,
		'hit_miss' => 0,
		'hit_total' => 0,
		'type' => 'wincache',
		);

	$info = wincache_ocache_fileinfo();
	$opcode_stats['hit_hit'] = $info['total_hit_count'];
	$opcode_stats['hit_miss'] = $info['total_miss_count'];
	$opcode_stats['hit_total'] = $info['total_hit_count'] + $info['total_miss_count'];

	$memory = wincache_ocache_meminfo();
	$opcode_stats['memory_avail'] = $memory['memory_free'];
	$opcode_stats['memory_total'] = $memory['memory_total'];
	$opcode_stats['memory_used'] = $memory['memory_total'] - $memory['memory_free'];

	$opcode_stats['memory_used'] /= $opcode_stats['memory_total'];
	$opcode_stats['memory_avail'] /= $opcode_stats['memory_total'];
	$opcode_stats['hit_hit'] /= $opcode_stats['hit_total'];
	$opcode_stats['hit_miss'] /= $opcode_stats['hit_total'];
}

// Make results easier to read
$opcode_stats['memory_used'] = round($opcode_stats['memory_used'], 2);
$opcode_stats['memory_avail'] = round($opcode_stats['memory_avail'], 2);
$opcode_stats['hit_hit'] = round($opcode_stats['hit_hit'], 2);
$opcode_stats['hit_miss'] = round($opcode_stats['hit_miss'], 2);


if ( $stat_flag ) {
	$opcode_stats['warning_check'] = (bool) ini_get($stat_flag);
	$smarty->assign('stat_flag', $stat_flag);
}

if ( isset($opcode_stats['hit_total']) ) {
	$opcode_stats = array_merge(
		$opcode_stats,
		array(
			'warning_fresh' => $opcode_stats['hit_total'] < 10000,
			'warning_ratio' => $opcode_stats['hit_hit'] < 0.8,
		)
	);
}

if ( isset($opcode_stats['memory_total']) ) {
	$opcode_stats = array_merge(
		$opcode_stats,
		array(
			'warning_starve' => $opcode_stats['memory_avail'] < 0.2,
			'warning_low' => $opcode_stats['memory_total'] < 60*1024*1024,
		)
	);
}

$smarty->assign('opcode_cache', $opcode_cache);
$smarty->assign('opcode_stats', $opcode_stats);

$txtUsed = tr('Used');
$txtAvailable = tr('Available');
if ($opcode_cache == 'WinCache') {
	// Somehow WinCache seems to flip the representations
	$txtAvailable = tr('Used');
	$txtUsed = tr('Available');
}
$smarty->assign(
	'memory_graph',
	$tikilib->httpScheme() . '://chart.apis.google.com/chart?' . http_build_query(
		array(
			'cht' => 'p3',
			'chs' => '250x100',
			'chd' => "t:{$opcode_stats['memory_used']},{$opcode_stats['memory_avail']}",
			'chl' => $txtUsed . '|' .$txtAvailable,
			'chtt' => tr('Memory'),
		),
		'',
		'&'
	)
);

$txtHit = tr('Hit');
$txtMiss = tr('Miss');
if ($opcode_cache == 'WinCache') {
	// Somehow WinCache seems to flip the representations
	$txtHit = tr('Miss');
	$txtMiss = tr('Hit');
}
$smarty->assign(
	'hits_graph',
	$tikilib->httpScheme() . '://chart.apis.google.com/chart?' . http_build_query(
		array(
			'cht' => 'p3',
			'chs' => '250x100',
			'chd' => "t:{$opcode_stats['hit_hit']},{$opcode_stats['hit_miss']}",
			'chl' => $txtHit . '|' . $txtMiss,
			'chtt' => tr('Cache'),
		),
		'',
		'&'
	)
);

// realpath_cache_size can make considerable difference on php performance apparently
if (function_exists('realpath_cache_size')) {
	$rpcs_current = realpath_cache_size();
	$rpcs_ini = ini_get('realpath_cache_size');
	$rpc_ttl = ini_get('realpath_cache_ttl');
	$smarty->assign('realpath_cache_size_current', $rpcs_current);
	$smarty->assign('realpath_cache_size_ini', $rpcs_ini);
	$smarty->assign('realpath_cache_ttl', $rpc_ttl);
	$smarty->assign('realpath_cache_size_percent', round($rpcs_current / TikiLib::lib('tiki')->return_bytes($rpcs_ini) * 100, 2));
}
