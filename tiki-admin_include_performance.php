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

$apc_used = false;

if( function_exists( 'apc_sma_info' ) && ini_get('apc.enabled') ) {
	$apc_used = true;
	
	$sma = apc_sma_info();
	$mem_total = $sma['num_seg'] * $sma['seg_size'];

	$cache = apc_cache_info( null, true );
	$hit_total = $cache['num_hits'] + $cache['num_misses'];

	$smarty->assign( 'apc_stats', array(
		'memory_used' => ( $mem_total - $sma['avail_mem'] ) / $mem_total,
		'memory_avail' => $sma['avail_mem'] / $mem_total,
		'memory_total' => $mem_total,
		'hit_hit' => $cache['num_hits'] / $hit_total,
		'hit_miss' => $cache['num_misses'] / $hit_total,
		'hit_total' => $hit_total,
		'warning_fresh' => $hit_total < 10000,
		'warning_ratio' => ( $cache['num_hits'] / $hit_total ) < 0.8,
		'warning_starve' => ( $sma['avail_mem'] / $mem_total ) < 0.2,
		'warning_low' => ( $mem_total < 32*1024*1024 ),
		'warning_check' => (bool) ini_get( 'apc.stat' ),
	) );
}

$smarty->assign( 'apc_used', $apc_used );
