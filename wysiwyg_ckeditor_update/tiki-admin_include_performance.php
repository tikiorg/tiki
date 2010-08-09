<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["performance"])) {
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

if( function_exists( 'apc_sma_info' ) && ini_get('apc.enabled') ) {
	$opcode_cache = 'APC';
	
	$sma = apc_sma_info();
	$mem_total = $sma['num_seg'] * $sma['seg_size'];

	$cache = apc_cache_info( null, true );
	$hit_total = $cache['num_hits'] + $cache['num_misses'];

	$stat_flag = 'apc.stat';
	$opcode_stats = array(
		'memory_used' => ( $mem_total - $sma['avail_mem'] ) / $mem_total,
		'memory_avail' => $sma['avail_mem'] / $mem_total,
		'memory_total' => $mem_total,
		'hit_hit' => $cache['num_hits'] / $hit_total,
		'hit_miss' => $cache['num_misses'] / $hit_total,
		'hit_total' => $hit_total,
	);
} elseif( function_exists( 'xcache_info' ) ) {
	$opcode_cache = 'XCache';

	if( ini_get( 'xcache.admin.enable_auth' ) ) {
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
		);

		foreach( range( 0, xcache_count( XC_TYPE_PHP ) - 1 ) as $index ) {
			$info = xcache_info( XC_TYPE_PHP, $index );

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
}

if( $stat_flag ) {
	$opcode_stats['warning_check'] = (bool) ini_get( $stat_flag );
	$smarty->assign( 'stat_flag', $stat_flag );
}

if( isset( $opcode_stats['hit_total'] ) ) {
	$opcode_stats = array_merge( $opcode_stats, array(
		'warning_fresh' => $opcode_stats['hit_total'] < 10000,
		'warning_ratio' => $opcode_stats['hit_hit'] < 0.8,
	) );
}

if( isset( $opcode_stats['memory_total'] ) ) {
	$opcode_stats = array_merge( $opcode_stats, array(
		'warning_starve' => $opcode_stats['memory_avail'] < 0.2,
		'warning_low' => $opcode_stats['memory_total'] < 60*1024*1024,
	) );
}

$smarty->assign( 'opcode_cache', $opcode_cache );
$smarty->assign( 'opcode_stats', $opcode_stats );
