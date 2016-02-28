<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_freetagged_info()
{
	return array(
		'name' => tra('Tagged'),
		'documentation' => 'PluginFreetagged',
		'description' => tra('List similarly tagged objects'),
		'prefs' => array('feature_freetags','wikiplugin_freetagged'),
		'iconname' => 'tag',
		'introduced' => 5,
		'params' => array(
			'tags' => array(
				'required' => false,
				'name' => tra('Tags to find similar to'),
				'description' => tra('Leave blank to use the object\'s own tags.'),
				'since' => '5.0',
				'filter' => 'text',
				'default' => ''
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Type of objects to extract. Set to All to find all types.'),
				'since' => '5.0',
				'filter' => 'text',
				'default' => null,
				'options' => array (
					array('text' => '', 'value' => ''),
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
				'description' => tra('Start record'),
				'since' => '5.0',
				'filter' => 'int',
				'default' => 0
			),
			'maxRecords' => array(
				'required' => false,
				'name' => tra('Max Records'),
				'description' => tra('Default -1 (all)'),
				'since' => '5.0',
				'filter' => 'int',
				'default' => -1
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tr('Determine sort order based on various fields (Default: %0)', '<code>created_desc</code>'),
				'since' => '5.0',
				'filter' => 'text',
				'default' => 'created_desc',
				'options' => array (
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Comments locked Ascending'), 'value' => 'comments_locked_asc'),
					array('text' => tra('Comments locked Descending'), 'value' => 'comments_locked_desc'),
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
				'since' => '5.0',
				'filter' => 'text',
				'default' => ''
			),
			'broaden' => array(
				'required' => false,
				'name' => tra('Choose whether to broaden'),
				'description' => tra('n|y'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'h_level' => array(
				'required' => false,
				'name' => tra('Heading Level'),
				'description' => tr('Choose the header level for formatting. Default is %0 (for header level h3). Set
					to %1 for no header tags.', '<code>3</code>', '<code>-1</code>'),
				'since' => '5.0',
				'filter' => 'int',
				'default' => '3'
			),
			'titles_only' => array(
				'required' => false,
				'name' => tra('Show Titles Only'),
				'description' => tra('Choose whether to show titles only (not shown by default)'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => tra('No'), 'value' => 'n'),
					array('text' => tra('Yes'), 'value' => 'y'), 
				)
			),
			'max_image_size' => array(
				'required' => false,
				'name' => tra('Max Image Size'),
				'description' => tr('Height or width in pixels. Default: %0 (no maximum)', '<code>0</code>'),
				'since' => '5.0',
				'filter' => 'digits',
				'default' => 0
			),
			'more' => array(
				'required' => false,
				'name' => tra('More'),
				'description' => tra('Show a \'more\' link that links to the full list of tagged objects (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'moreurl' => array(
				'required' => false,
				'name' => tra('More URL'),
				'description' => tra('Alternate "more" link pointing to specified URL instead of default full list of tagged objects'),
				'filter' => 'url',
				'default' => 'tiki-browse_freetags.php',
				'parent' => array('name' => 'more', 'value' => 'y'),
			),
			'moretext' => array(
				'required' => false,
				'name' => tra('More label'),
				'description' => tra('Alternate text to display on the "more" link (default is "more")'),
				'filter' => 'raw',
				'default' => 'more',
				'parent' => array('name' => 'more', 'value' => 'y'),
			),
		)
	);
}

function wikiplugin_freetagged($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$headerlib = TikiLib::lib('header');
	$freetaglib = TikiLib::lib('freetag');

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
		'more' => 'n',
		'moreurl' => 'tiki-browse_freetags.php',
		'moretext' => 'more',
	);
	
	$params = array_merge($defaults, $params);
	extract($params, EXTR_SKIP);
	
	if ($type == tra('all')) {
		$type = null;
	}
	
	$sort_mode = str_replace('created', 'o.`created`', $sort_mode);
	
	// We only display the "more" link if the number of displayed values is limited and there are more values than displayed
	// so we might need one more item just to know if there are more values than displayed
	if ( $maxRecords > 0 && $more == 'y' ) {
		$maxReturned = $maxRecords + 1;
	} else {
		$maxReturned = $maxRecords;
	}

	if ( !$tags && $object = current_object() ) {
		$tagArray = array();
		$ta = $freetaglib->get_tags_on_object($object['object'], $object['type']);
		foreach ($ta['data'] as $tag) {
			$tagArray[] = $tag['tag'];
		}
		
		if (!$type) {
			$type = $object['type'];
		}
		
		$objects = $freetaglib->get_similar($object['type'], $object['object'], $maxReturned, $type);
		
	} else {
		$tagArray = $freetaglib->_parse_tag($tags);
		$objects = $freetaglib->get_objects_with_tag_combo($tagArray, $type, '', 0, $maxReturned, $sort_mode, $find, $broaden);
		$objects = $objects['data'];
	}
	
	if ( $more == 'y' && count($objects) == $maxReturned ) {
		array_pop($objects);
		$smarty->assign('more','y');
	} else {
		$smarty->assign('more','n');
	}

	$moreurlparams = 'tag='.$tags.'&old_type='.urlencode($type).'&sort_mode='.urlencode($params['sort_mode']).'&find='.urlencode($find).'&broaden='.urlencode($broaden);
	if ( strpos($moreurl,'?') === FALSE ) {
		$moreurl = $moreurl . '?' . $moreurlparams;
	} else {
		$moreurl = $moreurl . '&' . $moreurlparams;
	}
	$smarty->assign_by_ref('moreurl', $moreurl);

	if ( isset($moretext) ) {
		$smarty->assign_by_ref('moretext', $moretext);
	} else {
		$smarty->assign('moretext', 'more');
	}

	foreach ($objects as &$obj) {
		if ($titles_only == 'n') {
			switch ($obj['type']) {
				case  'article':
					$artlib = TikiLib::lib('art');
					$info = $artlib->get_article($obj['itemId']);
					$obj['date'] = $info['publishDate'];
					$obj['description'] = $tikilib->parse_data($info['heading']);
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
					$filegallib = TikiLib::lib('filegal');
					$info = $filegallib->get_file($obj['itemId']);
					$obj['description'] = $info['description'];
					$obj['date'] = $info['lastModif'];
					include_once 'lib/wiki-plugins/wikiplugin_img.php';
					$imgparams = array('fileId' => $obj['itemId'], 'thumb' => 'box');
					if ($max_image_size > 0) {
						$imgparams['max'] = $max_image_size;
					}
					
					$obj['img'] = wikiplugin_img('', $imgparams, 0);
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
