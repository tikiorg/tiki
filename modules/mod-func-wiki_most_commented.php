<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_wiki_most_commented_info()  {
	return array(
		'name' => tra('Most commented wiki pages'),
		'description' => tra('Displays the specified number of the blog post comments most recently added.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_wiki_most_commented( $mod_reference, $module_params ) {
	global $smarty;
	global $dbTiki;
	global $commentslib;
	if(!isset($commentslib)){
		include_once ('lib/commentslib.php');
		$commentslib = new Comments($dbTiki);
	}
	
	$lang = '';
	if(isset($module_params['lang'])){
		$lang = $module_params['lang'];
	}
	$result = $commentslib->order_comments_by_count('wiki', $lang, $mod_reference['rows']);
	
	$smarty->assign('modWikiMostCommented', $result['data']);
}
