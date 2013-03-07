<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_bloglist_info()
{
	return array(
		'name' => tra('Blog List'),
		'documentation' => 'PluginBlogList',		
		'description' => tra('Display posts from a site blog'),
		'prefs' => array( 'feature_blogs', 'wikiplugin_bloglist' ),
		'icon' => 'img/icons/text_list_bullets.png',
		'params' => array(
			'Id' => array(
				'required' => true,
				'name' => tra('Blog ID'),
				'description' => tra('The ID number of the blog on the site you wish to list posts from'),
				'filter' => 'digits',
				'default' => ''
			),
			'Items' => array(
				'required' => false,
				'name' => tra('Maximum Items'),
				'description' => tra('Maximum number of entries to list (no maximum set by default)'),
				'filter' => 'digits',
				'default' => ''
			),
			'author' => array(
				'required' => false,
				'name' => tra('Author'),
				'description' => tra('Only display posts created by this user (all posts listed by default)'),
				'default' => ''
			),
			'simpleList' => array(
				'required' => false,
				'name' => tra('Simple List'),
				'description' => tra('Show simple list of date, title and author (default=y) or formatted list of blog posts (n)'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'dateStart' => array(
				'required' => false,
				'name' => tra('Start Date'),
				'description' => tra('Earliest date to select posts from.') . ' (YYYY-MM-DD)',
				'filter' => 'date',
				'default' => ''
			),
			'dateEnd' => array(
				'required' => false,
				'name' => tra('End Date'),
				'description' => tra('Latest date to select posts from.') . ' (YYYY-MM-DD)',
				'filter' => 'date',
				'default' => ''
			),
			'containerClass' => array(
				'required' => false,
				'name' => tra('Container Class'),
				'description' => tra('CSS Class to add to the container DIV.article. (Default="wikiplugin_bloglist")'),
				'filter' => 'striptags',
				'default' => 'wikiplugin_bloglist'
			),
		),
	);
}

function wikiplugin_bloglist($data, $params)
{
	global $tikilib, $smarty, $user;

	if (!isset($params['Id'])) {
		TikiLib::lib('errorreport')->report(tra('missing blog Id for BLOGLIST plugins'));
		return '';
	}

	if (!isset($params['Items'])) $params['Items'] = -1;
	if (!isset($params['offset'])) $params['offset'] = 0;
	if (!isset($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
	if (!isset($params['find'])) $params['find'] = '';
	if (!isset($params['author'])) $params['author'] = '';
	if (!isset($params['simpleList'])) $params['simpleList'] = 'y';
	if (!isset($params['isHtml'])) $params['isHtml'] = 'n';

	if (isset($params['dateStart'])) {
		$dateStartTS = strtotime($params['dateStart']);
	}
	if (isset($params['dateEnd'])) {
		$dateEndTS = strtotime($params['dateEnd']);
	}
	$dateStartTS = !empty($dateStartTS) ? $dateStartTS : 0;
	$dateEndTS = !empty($dateEndTS) ? $dateEndTS : $tikilib->now;

	if (!isset($params['containerClass'])) {
		$params['containerClass'] = 'wikiplugin_bloglist';
	}
	$smarty->assign('container_class', $params['containerClass']);
	
	if ($params['simpleList'] == 'y') {
		global $bloglib; require_once('lib/blogs/bloglib.php');
		$blogItems = $bloglib->list_posts($params['offset'], $params['Items'], $params['sort_mode'], $params['find'], $params['Id'], $params['author'], '', $dateStartTS, $dateEndTS);
		$smarty->assign_by_ref('blogItems', $blogItems['data']);
		$template = 'wiki-plugins/wikiplugin_bloglist.tpl';
	} else {
		global $bloglib; include_once('lib/blogs/bloglib.php');
		
		$blogItems = $bloglib->list_blog_posts($params['Id'], false, $params['offset'], $params['Items'], $params['sort_mode'], $params['find'], $dateStartTS, $dateEndTS);

		$blog_data = TikiLib::lib('blog')->get_blog($params['Id']);
		$smarty->assign('blog_data', $blog_data);

		$smarty->assign('ownsblog', $user && $user == $blog_data["user"] ? 'y' : 'n');

		$smarty->assign('show_heading', 'n');
		$smarty->assign('use_author', 'y');
		$smarty->assign('add_date', 'y');
		$smarty->assign_by_ref('listpages', $blogItems['data']);
		$template = 'tiki-view_blog.tpl';
	}
	$ret = $smarty->fetch($template);
	return '~np~'.$ret.'~/np~';
}
