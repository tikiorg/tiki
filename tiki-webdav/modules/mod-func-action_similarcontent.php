<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_action_similarcontent_info(){
	return array(
		'name' => tra('Similar Content'),
		'description' => tra('Find similar content based on FreeTags.'),
		'prefs' => array("feature_freetags"),
		'params' => array(
			'contentType' => array(
				'name' => tra('Similar Content Filter'),
				'description' => tra('Display only similar content of type specified') . " " . tra('Default: "All Content Type".') . " " . tra('Options: "article, wiki page, blog post".')
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_action_similarcontent( $mod_reference, $module_params ) {
	global $smarty, $freetaglib;

	include_once ('lib/freetag/freetaglib.php');

	$filterType = '';
	if(isset($module_params)){
		$filterType = $module_params['contentType'];
	}
		
	$currentContentType = "article";
	if(isset($_REQUEST['articleId']))
	{
		$currentContentType = "article";
		$contentId = $_REQUEST['articleId'];
	}
	else
	{
		if(isset($_REQUEST['postId']))
		{
			$currentContentType = "blog post";
			$contentId = $_REQUEST['postId'];
		}
		else 
		{
			if(isset($_REQUEST['page']))
			{
				$currentContentType = "wiki page";
				$contentId = $_REQUEST['page'];
			}
		}
	}
	
	
	
	if(isset($contentId)) {
		
		$tags = $freetaglib->get_tags_on_object($contentId, $currentContentType);
		
		foreach($tags['data'] as $tag){
			$allTags[] = $tag['tag'];
		}
		
			
		$similarContent = $freetaglib->get_objects_with_tag_combo($allTags, $filterType, '', 0, -1, 'name_asc', '', 'y');
			
		foreach($similarContent['data'] as $item){
			if($item['type'] != $currentContentType){
				$relatedExclusiveContent[] = $item;
			}
			else {			
				if($item['itemId'] != $contentId) {
					$relatedExclusiveContent[] = $item;
				}
			}
		}
		$smarty->assign('similarContent', $relatedExclusiveContent);
	}
	
	//$smarty->assign('modLastBlogPosts', $ranking["data"]);
}