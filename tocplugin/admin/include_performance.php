<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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


$opcode_stats = TikiLib::lib('admin')->getOpcodeCacheStatus();
$stat_flag = $opcode_stats['stat_flag'];
if ( $stat_flag ) {
	$smarty->assign('stat_flag', $stat_flag);
}

$opcode_cache = $opcode_stats['opcode_cache'];
$smarty->assign('opcode_cache', $opcode_cache);
$smarty->assign('opcode_stats', $opcode_stats);

$txtUsed = tr('Used');
$txtAvailable = tr('Available');
$smarty->assign(
	'memory_graph',
	$tikilib->httpScheme() . '://chart.googleapis.com/chart?' . http_build_query(
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
$smarty->assign(
	'hits_graph',
	$tikilib->httpScheme() . '://chart.googleapis.com/chart?' . http_build_query(
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
