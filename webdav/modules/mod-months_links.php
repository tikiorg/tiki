<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $prefs, $sections, $smarty;

$link = '';
if ( isset($module_params['feature'])
	&& isset($sections[$module_params['feature']])
	&& isset($sections[$module_params['feature']]['feature'])
	&& $prefs[$sections[$module_params['feature']]['feature']] == 'y'
) {
	$default_date_args = 'date_min=%d&amp;date_max=%d';
	switch ( $module_params['feature'] ) {
		case 'blogs':
			if ( isset($module_params['id']) ) {
				$link = 'tiki-view_blog.php?blogId='.$module_params['id'].'&amp;'.$default_date_args;
				$object_key = 'itemObjectType';
			}
			break;
		case 'cms':
			$link = 'tiki-view_articles.php?'.$default_date_args;
			$object_key = 'objectType';
			break;
	}
}

$months = array();
$title = '';
if ( $link != '' ) {
	global $tikilib;
	$month_names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$current_month_num = TikiLib::date_format("%m", $tikilib->now);
	$current_year = TikiLib::date_format("%Y", $tikilib->now);
	$timestamp_month_start = 0;
	if ( $module_rows <= 0 ) $module_rows = 12;

	for ( $i = 0 ; $i < $module_rows ; $i++, $current_month_num-- ) {
		if ( $current_month_num == 0 ) {
			$current_month_num = 12;
			$current_year--;
		}

		$month_name = ucfirst(tra($month_names[$current_month_num - 1])).' '.$current_year;
		if ( $timestamp_month_start > 0 ) {
			$timestamp_month_end = $timestamp_month_start - 1;
		} else {
			$timestamp_month_end = TikiLib::make_time(0, 0, 0, $current_month_num + 1, 1, $current_year) - 1;
		}
		$timestamp_month_start = TikiLib::make_time(0, 0, 0, $current_month_num, 1, $current_year);
		$months[$month_name] = sprintf($link, $timestamp_month_start, $timestamp_month_end);
	}
	$title = ucwords($sections[$module_params['feature']][$object_key]).' - '.tra('List by month');
}
$smarty->assign('months', $months);
$smarty->assign('tpl_module_title', $title);
