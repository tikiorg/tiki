<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function module_months_links_info() {
	return array(
		'name' => tra('Months links'),
		'description' => tra('Links to the objects of a given type for the current month and those preceding it.'),
		'prefs' => array(),
		'params' => array(
			'feature' => array(
				'name' => tra('Object type'),
				'description' => tra('Type of objects to link to.') . " " . tra('Possible values:') . ' ' . tra('"blogs" for blog posts, "cms" for published articles.'),
				'required' => true
			),
			'id' => array(
				'name' => tra('Object identifier'),
				'description' => tra('Identifier of an object with children to link to.') . ' ' .tra('This is required for the blog Object type.') . " " . tra('Example values:') . ' 3, 14.' . tra('For example, an identifier of 3 and a blogs Object type will show links to the blog posts in the blog with identifier 3.')
			)
		),
		'common_params' => array('nonums','rows')
	);
}

function module_months_links( $mod_reference, $module_params ) {
	global $prefs, $sections, $smarty;

	if ( isset($module_params['feature'])
		&& isset($sections[$module_params['feature']])
		&& isset($sections[$module_params['feature']]['feature'])
		&& $prefs[$sections[$module_params['feature']]['feature']] == 'y'
	) {
		$default_date_args = 'date_min=%d&amp;date_max=%d';
		switch ( $module_params['feature'] ) {
			case 'blogs':
				if ($prefs['feature_blogs'] == 'y' && isset($module_params['id']) ) {
					$link = 'tiki-view_blog.php?blogId='.$module_params['id'].'&amp;'.$default_date_args;
					$object_key = 'itemObjectType';
				}
				break;
			case 'cms':
				if ($prefs['feature_articles'] == 'y') {
					$link = 'tiki-view_articles.php?'.$default_date_args;
					$object_key = 'objectType';
				}
				break;
		}
	}
	
	if ( isset($link)) {
		global $tikilib, $bloglib;
		include_once ('lib/blogs/bloglib.php');
		$month_names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$current_month_num = TikiLib::date_format("%m", $tikilib->now);
		$current_year = TikiLib::date_format("%Y", $tikilib->now);
		$timestamp_month_start = 0;

		$months = array();
		for ( $i = 0 ; $i < ($mod_reference['rows'] > 0 ? $mod_reference['rows'] : 12) ; $i++, $current_month_num-- ) {
			if ( $current_month_num == 0 ) {
				$current_month_num = 12;
				$current_year--;
			}
	
			$month_name = ucfirst(tra($month_names[$current_month_num - 1])).' '.$current_year;
			if ( $timestamp_month_start > 0 ) {
				$timestamp_month_end = $timestamp_month_start - 1;
			} else {
				$timestamp_month_end = $tikilib->make_time(0, 0, 0, $current_month_num + 1, 1, $current_year) - 1;
			}
			$timestamp_month_start = $tikilib->make_time(0, 0, 0, $current_month_num, 1, $current_year);
			$posts_of_month = $bloglib->list_blog_posts($module_params['id'],true,0,-1,'created_desc','',$timestamp_month_start,$timestamp_month_end);
			if( $posts_of_month["cant"] > 0 ) {
				$months[$month_name." [".$posts_of_month["cant"]."]"] = sprintf($link, $timestamp_month_start, $timestamp_month_end);
			}
		}
		$title = ucwords($sections[$module_params['feature']][$object_key]).' - '.tra('List by month');
		$smarty->assign('months', $months);
		$smarty->assign('tpl_module_title', $title);
	}
}
