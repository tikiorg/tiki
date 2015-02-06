<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_action_similarcontent_info()
{
	return array(
		'name' => tra('Similar Content'),
		'description' => tra('Find similar content based on tags.'),
		'prefs' => array("feature_freetags"),
		'params' => array(
			'contentType' => array(
				'name' => tra('Similar Content Filter'),
				'description' => tra('Display only similar content of type specified') . " " . tra('Default: "All Content Type".') . " " . tra('Options: "article, wiki page, blog post".')
			),
			'broaden' => array(
				'name' => tra('Broaden FreeTag Search'),
				'description' => tra('Find similar content that contains one of the Tags or All of the Tags') .
															" " . tra('Default: "n - needs to contain all of the Tags".') .
															" " . tra('Options: "n - Needs to contain All Tags / y - Needs to contain one of the Tags".')
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_action_similarcontent($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$freetaglib = TikiLib::lib('freetag');

	$filterType = '';
	if (isset($module_params['contentType'])) {
		$filterType = $module_params['contentType'];
	}
	
	$broaden = 'n';
	if (isset($module_params['broaden'])) {
		$broaden = $module_params['broaden'];
	}
		
	$currentContentType = "article";
	if (isset($_REQUEST['articleId'])) {
		$currentContentType = "article";
		$contentId = $_REQUEST['articleId'];
	} else {
		if (isset($_REQUEST['postId'])) {
			$currentContentType = "blog post";
			$contentId = $_REQUEST['postId'];
		} else {
			if (isset($_REQUEST['page'])) {
				$currentContentType = "wiki page";
				$contentId = $_REQUEST['page'];
			}
		}
	}
	
	if (isset($contentId)) {
		
		$tags = $freetaglib->get_tags_on_object($contentId, $currentContentType);
		$allTags = array();
		foreach ($tags['data'] as $tag) {
			$allTags[] = $tag['tag'];
		}
			
		$similarContent = $freetaglib->get_objects_with_tag_combo($allTags, $filterType, '', 0, $mod_reference['rows'], 'name_asc', '', $broaden);
		$relatedExclusiveContent = array();	

		foreach ($similarContent['data'] as $item) {
			if ($item['type'] != $currentContentType) {
				$relatedExclusiveContent[] = $item;
			} else {			
				if ($item['itemId'] != $contentId) {
					$relatedExclusiveContent[] = $item;
				}
			}
		}
		$smarty->assign('similarContent', $relatedExclusiveContent);
	}
	
	//$smarty->assign('modLastBlogPosts', $ranking["data"]);
}
