<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_months_links_info()
{
	return array(
		'name' => tra('Months Links'),
		'description' => tra('Link to articles or blog posts for the current month and those preceding it.'),
		'prefs' => array(),
		'params' => array(
			'feature' => array(
				'name' => tra('Object Type'),
				'description' => tra('Type of objects to link to.') . ' ' . tra('Possible values:') . ' ' . tra('"blogs" for blog posts, "cms" for published articles.'),
				'required' => true
			),
			'id' => array(
				'name' => tra('Object Identifier'),
				'description' => tra('Identifier of an object with children to link to.') . ' ' .tra('This is required for the blog Object type.') . ' ' . tra('Example values:') . ' 3, 14.' . tra('For example, an identifier of 3 and a blogs Object type will show links to the blog posts in the blog with identifier 3.')
			)
		),
		'common_params' => array('rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_months_links($mod_reference, $module_params)
{
	global $prefs, $sections;
	$smarty = TikiLib::lib('smarty');

	if (isset($module_params['feature'])
		&& isset($sections[$module_params['feature']])
		&& isset($sections[$module_params['feature']]['feature'])
		&& $prefs[$sections[$module_params['feature']]['feature']] == 'y'
	) {
		$default_date_args = 'date_min=%d&amp;date_max=%d';
		switch ($module_params['feature']) {
			case 'blogs':
				if ($prefs['feature_blogs'] == 'y' && isset($module_params['id'])) {
					$link = 'tiki-view_blog.php?blogId=' . $module_params['id'] . '&amp;' . $default_date_args;
					$object_key = 'itemObjectType';
				}
				break;

			case 'cms':
				if ($prefs['feature_articles'] == 'y') {
					$link = 'tiki-view_articles.php?' . $default_date_args;
					$object_key = 'objectType';
				}
				break;
		}
	}

	if (isset($link)) {
		$tikilib = TIkiLib::lib('tiki');
		if ($module_params['feature'] == 'blogs') {
			$bloglib = TikiLib::lib('blog');
		} elseif ($module_params['feature'] == 'cms') {
			$artlib = TikiLib::lib('art');
		}

		$month_names = array(
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
		);

		$current_month_num = TikiLib::date_format('%m', $tikilib->now);
		$current_year = TikiLib::date_format('%Y', $tikilib->now);
		$timestamp_month_start = 0;

		if ($_SESSION['cms_last_viewed_month'] && $module_params['feature'] == 'cms') {
			list($year_expanded,$month_expanded_num) = explode('-', $_SESSION['cms_last_viewed_month']);
			$month_expanded = $month_names[$month_expanded_num-1];
		} elseif ($_SESSION['blogs_last_viewed_month'] && $module_params['feature'] == 'blogs') {
			list($year_expanded,$month_expanded_num) = explode('-', $_SESSION['blogs_last_viewed_month']);
			$month_expanded = $month_names[$month_expanded_num-1];
		} else {
			$year_expanded = $current_year;
			$month_expanded = $month_names[$current_month_num-1];
		}
		$archives = array();
		$numrows = $mod_reference['rows'] > 0 ? $mod_reference['rows'] : 120;

		for ($i = 0 ; $i < $numrows ; $i++, $current_month_num--) {
			if ($current_month_num == 0) {
				$current_month_num = 12;
				$current_year--;
			}

			$real_month_name = ucfirst(tra($month_names[$current_month_num - 1]));

			if ($timestamp_month_start > 0) {
				$timestamp_month_end = $timestamp_month_start - 1; // Optimisation to save one make_time() call per iteration
			} else {
				$timestamp_month_end = $tikilib->make_time(0, 0, 0, $current_month_num + 1, 1, $current_year) - 1;
			}

			$timestamp_month_start = $tikilib->make_time(0, 0, 0, $current_month_num, 1, $current_year);

			if ($module_params['feature'] == 'blogs') {
				$posts_of_month = $bloglib->list_blog_posts($module_params['id'], true, 0, -1, 'created_desc', '', $timestamp_month_start, $timestamp_month_end);
				if ($posts_of_month['cant'] > 0) {
					$archives[$current_year]['monthlist'][$real_month_name]['link'] = sprintf($link, $timestamp_month_start, $timestamp_month_end);
					$archives[$current_year]['monthlist'][$real_month_name]['cant'] = $posts_of_month['cant'];
					// Clicking on the year number displays the first non-empty month
					if (!isset($archives[$current_year]['link'])) {
						$archives[$current_year]['link'] = $archives[$current_year]['monthlist'][$real_month_name]['link'];
					}
					$archives[$current_year]['cant'] += $posts_of_month['cant'];
					for ($post=0; $post < $posts_of_month['cant']; $post++) {
						$archives[$current_year]['monthlist'][$real_month_name]['postlist'][$posts_of_month['data'][$post]['postId']] = $posts_of_month['data'][$post]['title'];
					}
				}
			} elseif ($module_params['feature'] == 'cms') {
				$posts_of_month = $artlib->list_articles(
					0,
					-1,
					'publishDate_desc',
					'',
					$timestamp_month_start,
					$timestamp_month_end,
					false,
					'',
					'',
					'y',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					false,
					'',
					''
				);

				if ($posts_of_month["cant"] > 0) {
					$archives[$current_year]['monthlist'][$real_month_name]['link'] = sprintf($link, $timestamp_month_start, $timestamp_month_end);
					$archives[$current_year]['monthlist'][$real_month_name]['cant'] = $posts_of_month['cant'];
					// Clicking on the year number displays the first non-empty month
					if (!isset($archives[$current_year]['link'])) {
						$archives[$current_year]['link'] = $archives[$current_year]['monthlist'][$real_month_name]['link'];
					}
					$archives[$current_year]['cant'] += $posts_of_month['cant'];

					for ($post=0; $post < $posts_of_month['cant']; $post++) {
						$archives[$current_year]['monthlist'][$real_month_name]['postlist'][$posts_of_month['data'][$post]['articleId']] = $posts_of_month['data'][$post]['title'];
					}
				}
			}
		}
		$title = ucwords($sections[$module_params['feature']][$object_key]) . ' - ' . tra('List by month');
		$smarty->assign('feature', $module_params['feature']);
		$smarty->assign('archives', $archives);
		$smarty->assign('year_expanded', $year_expanded);
		$smarty->assign('month_expanded', $month_expanded);
		$smarty->assign('module_id', $mod_reference['moduleId']);
		$smarty->assign('tpl_module_title', $title);
	} else {	// We don't know if this is used for blogs or articles
		$title = tra('List by month');
		$smarty->assign('tpl_module_title', $title);
	}
}
