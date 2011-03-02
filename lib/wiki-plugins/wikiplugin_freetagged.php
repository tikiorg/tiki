<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Freetagged plugin (derived from Title Search Plugin)
 * Find all similar tagged objects
 */

function wikiplugin_freetagged_help() {
	return tra("Search the titles of all pages in this wiki").":<br />~np~{FREETAGGED(search=>Admin,info=>hits|user,exclude=>HomePage|SandBox,noheader=>0)}{FREETAGGED}~/np~";
}

function wikiplugin_freetagged_info() {
	return array(
		'name' => tra('Freetagged'),
		'documentation' => 'PluginFreetagged',
		'description' => tra('List similarly tagged objects'),
		'prefs' => array( 'wikiplugin_freetagged' ),
		'icon' => 'pics/icons/tag_blue.png',
		'params' => array(
			'tags' => array(
				'required' => false,
				'name' => tra('Tags to find similar to'),
				'description' => tra('Leave blank to use the object\'s own tags.'),
				'filter' => 'text',
				'default' => ''
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Type of objects to extract. Set to All to find all types.'),
				'filter' => 'text',
				'default' => null,
				'options' => array (
					array('text' => tra('Same'), 'value' => 'all'),
					array('text' => tra('All'), 'value' => 'all'),
					array('text' => tra('Wiki Pages'), 'value' => 'wiki page'),
					array('text' => tra('Blog Posts'), 'value' => 'blog post'),
					array('text' => tra('Article'), 'value' => 'article'),
					array('text' => tra('Directory'), 'value' => 'directory'),
					array('text' => tra('Faqs'), 'value' => 'faq'),
					array('text' => tra('File Galleries'), 'value' => 'file gallery'),
					array('text' => tra('Files'), 'value' => 'file'),
					array('text' => tra('Polls'), 'value' => 'poll'),
					array('text' => tra('Quizzes'), 'value' => 'quiz'),
					array('text' => tra('Surveys'), 'value' => 'survey'),
					array('text' => tra('Trackers'), 'value' => 'tracker'),
				),
			),
			'offset' => array(
				'required' => false,
				'name' => tra('Offset'),
				'description' => tra('Start record.'),
				'filter' => 'text',
				'default' => 0
			),
			'maxRecords' => array(
				'required' => false,
				'name' => tra('Max Records'),
				'description' => tra('Default -1 (all)'),
				'filter' => 'text',
				'default' => -1
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('Choose from:  objectId, type, itemId, description, created, name, href, hits, comments_locked (Default: created_desc)'),
				'filter' => 'text',
				'default' => 'created_desc',
				'options' => array (
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Comments Locked Ascending'), 'value' => 'comments_locked_asc'),
					array('text' => tra('Comments Locked Descending'), 'value' => 'comments_locked_desc'),
					array('text' => tra('Created Ascending'), 'value' => 'created_asc'),
					array('text' => tra('Created Descending'), 'value' => 'created_desc'),
					array('text' => tra('Description Ascending'), 'value' => 'description_asc'),
					array('text' => tra('Description Descending'), 'value' => 'description_desc'),
					array('text' => tra('Hits Ascending'), 'value' => 'hits_asc'),
					array('text' => tra('Hits Descending'), 'value' => 'hits_desc'),
					array('text' => tra('Href Ascending'), 'value' => 'href_asc'),
					array('text' => tra('Href Descending'), 'value' => 'href_desc'),
					array('text' => tra('Item ID Ascending'), 'value' => 'itemid_asc'),
					array('text' => tra('Item ID Descending'), 'value' => 'itemid_desc'),
					array('text' => tra('Name Ascending'), 'value' => 'name_asc'),
					array('text' => tra('Name Descending'), 'value' => 'name_desc'),
					array('text' => tra('Object ID Ascending'), 'value' => 'objectid_asc'),
					array('text' => tra('Object ID Descending'), 'value' => 'objectid_desc'),
					array('text' => tra('Type Ascending'), 'value' => 'type_asc'),
					array('text' => tra('Type Descending'), 'value' => 'type_desc'),
				),
			),
			'find' => array(
				'required' => false,
				'name' => tra('Find'),
				'description' => tra('Show objects with names or descriptions similar to the text entered here'),
				'filter' => 'text',
				'default' => ''
			),
			'broaden' => array(
				'required' => false,
				'name' => tra('Broaden'),
				'description' => tra('n|y'),
				'filter' => 'text',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'h_level' => array(
				'required' => false,
				'name' => tra('Header Level'),
				'description' => tra('Choose the header level for formatting. Default is 3 (for header level h3). Set to -1 for no header tags.'),
				'filter' => 'int',
				'default' => '3'
			),
			'titles_only' => array(
				'required' => false,
				'name' => tra('Show Titles Only'),
				'description' => tra('Choose whether to show titles only (not shown by default)'),
				'filter' => 'text',
				'default' => 'n',
				'options' => array(
					array('text' => tra('No'), 'value' => 'n'),
					array('text' => tra('Yes'), 'value' => 'y'), 
				)
			),
			'max_image_size' => array(
				'required' => false,
				'name' => tra('Maximum Image Size'),
				'description' => tra('Height or width in pixels. Default = 0 (no maximum)'),
				'filter' => 'text',
				'default' => 0
			)
		)
	);
}

function wikiplugin_freetagged($data, $params) {
	global $freetaglib, $smarty, $tikilib, $headerlib;
	include_once('lib/freetag/freetaglib.php');

	$defaults =  array(
        'tags' => '',
        'type' => null,
		'offset' => 0,
		'maxRecords' => -1,
		'sort_mode' => 'created_desc',
		'find' => '',
		'broaden' => 'n',
		'h_level' => '3',
		'titles_only' => 'n',
		'max_image_size' => 0,
	);
	
	$params = array_merge($defaults, $params);
	extract ($params, EXTR_SKIP);
	
	if ($type == tra('all')) { $type = null; }
	
	$sort_mode = str_replace('created', 'o.`created`', $sort_mode);
	
	if ( !$tags && $object = current_object() ) {
		$tagArray = array();
		$ta = $freetaglib->get_tags_on_object($object['object'], $object['type']);
		foreach($ta['data'] as $tag) {
			$tagArray[] = $tag['tag'];
		}
		
		if (!$type) { $type = $object['type']; }
		
		$objects = $freetaglib->get_similar( $object['type'], $object['object'], $maxRecords , $type );
		
	} else {
		$tagArray = $freetaglib->_parse_tag($tags);
		$objects = $freetaglib->get_objects_with_tag_combo($tagArray, $type, '', 0, $maxRecords, $sort_mode, $find, $broaden);
		$objects = $objects['data'];
	}
	
	foreach($objects as &$obj) {
		if ($titles_only == 'n') {
			switch ($obj['type']) {
				case  'article':
					global $artlib; include_once('lib/articles/artlib.php');
					$info = $artlib->get_article($obj['itemId']);
					$obj['date'] = $info['publishDate'];
					$obj['description'] = $tikilib->parse_data( $info['heading']);
					if ($info['useImage'] == 'y') {
						$obj['image'] = 'article_image.php?id='.$obj['itemId'];
					} else if (!empty($info['topicId'])) {
						$obj['image'] = 'article_image.php?image_type=topic&amp;id='.$info['topicId'];
					}
					if (isset($obj['image'])) {
						if (!empty($info['image_x'])) {
							$w = $info['image_x'];
						} else {
							$w = 0;
						}
						if (!empty($info['image_y'])) {
							$h = $info['image_y'];
						} else {
							$h = 0;
						}
						if ($max_image_size > 0) {
							if ($w > $h && $w > $max_image_size) {
								$w = $max_image_size;
								$h = floor($w * $h / $info['image_x']);
							} else if ($h > $max) {
								$h = $max_image_size;
								$w = floor($h * $w / $info['image_y']);	
							}
							
						}
						$obj['img'] = '<img  src="'.$obj['image'] . ($w ? ' width="'.$w.'"' : '') . ($h ? ' height="'.$h.'"' : '') .'"/>';
					}
					break;
				case 'file':
					global $filegallib; include_once('lib/filegals/filegallib.php');
					$info = $filegallib->get_file($obj['itemId']);
					$obj['description'] = $info['description'];
					$obj['date'] = $info['lastModif'];
					include_once 'lib/wiki-plugins/wikiplugin_img.php';
					$imgparams = array('fileId' => $obj['itemId'], 'rel' => 'box[g]');
					$imgparams['thumb'] = 'y';
					if ($max_image_size > 0) {
						$imgparams['max'] = $max_image_size;
					}
					
					$obj['img'] = wikiplugin_img( '', $imgparams, 0 );
					$obj['img'] = str_replace('~np~', '', $obj['img']);	// don't nest ~np~
					$obj['img'] = str_replace('~/np~', '', $obj['img']);
					break;
				case 'wiki page':
					$info = $tikilib->get_page_info($obj['name'], false);
					$obj['description'] = $info['description'];
					$obj['date'] = $info['lastModif'];
					$obj['image'] = '';
					break;
				default:
					$obj['description'] = '';
					$obj['image'] = '';
					$obj['date'] = '';
			}
		} else {
			$obj['description'] = '';
			$obj['image'] = '';
			$obj['date'] = '';
		}
	}

	$smarty->assign_by_ref('objects', $objects);
	$smarty->assign('h_level', $h_level);
	
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_freetagged.tpl');
	return '~np~'.$ret.'~/np~';
	
}


