<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
    	'description' => tra('Find similarly tagged objects'),
    	'prefs' => array( 'wikiplugin_freetagged' ),
    	'params' => array(
    		'tags' => array(
    			'required' => false,
    			'name' => tra('Tags to find similar to'),
    			'description' => tra("Leave blank to use the object's own tags."),
				'filter' => 'text',
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Type of objects to extract. Set to All to find all types.'),
				'filter' => 'text',
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
			),
			'maxRecords' => array(
				'required' => false,
				'name' => tra('Max Records'),
				'description' => tra('Default -1 (all)'),
				'filter' => 'text',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('Default: type_asc,name_asc'),
				'filter' => 'text',
			),
			'find' => array(
				'required' => false,
				'name' => tra('Find'),
				'description' => tra(''),
				'filter' => 'text',
			),
			'broaden' => array(
				'required' => false,
				'name' => tra('Broaden'),
				'description' => tra('n|y'),
				'filter' => 'text',
			),
			'h_level' => array(
				'required' => false,
				'name' => tra('Header level'),
				'description' => tra('Default: 3'),
				'filter' => 'text',
			),
		),
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
		'sort_mode' => 'type_asc,name_asc',
		'find' => '',
		'broaden' => 'n',
		'h_level' => '3',
	);
	
	extract (array_merge($defaults, $params), EXTR_SKIP);
	
	if ($type == tra('all')) { $type = null; }
	
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
		$objects = $freetaglib->get_objects_with_tag_combo($tagArray, $type, '', $offset, $maxRecords, $sort_mode, $find, $broaden);
		$objects = $objects['data'];
	}
	
	foreach($objects as &$obj) {
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
					$obj['img'] = '<img  src="'.$obj['image'] . (!empty($info['image_x']) ? ' width="'.$info['image_x'].'"' : '') .
						 (!empty($info['image_y']) ? ' height="'.$info['image_x'].'"' : '') .'"/>';
				}
				break;
			case 'file':
				global $filegallib; include_once('lib/filegals/filegallib.php');
				$info = $filegallib->get_file($obj['itemId']);
				$obj['description'] = $info['description'];
				$obj['date'] = $info['lastModif'];
				include_once 'lib/wiki-plugins/wikiplugin_img.php';
				$obj['img'] = wikiplugin_img( '', array('fileId' => $obj['itemId'], 'thumb' => 'y', 'rel' => 'box[g]'), 0 );
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
	}

	$smarty->assign_by_ref('objects', $objects);
	$smarty->assign('h_level', $h_level);
	
	$headerlib->add_css(<<<CSS
ul.freetagged li {
	float: left;
	clear: left;
	list-style: none;
}

CSS
	);
	
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_freetagged.tpl');
	return '~np~'.$ret.'~/np~';
	
}


