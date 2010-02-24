<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_articles_most_commented_info()  {
	return array(
		'name' => tra('Most commented articles'),
		'description' => tra('Displays the specified number of the blog post comments most recently added.'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_articles_most_commented( $mod_reference, $module_params ) {
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
	$result = $commentslib->order_comments_by_count('article', $lang, $mod_reference['rows']);
	
	$smarty->assign('modArticlesMostCommented', $result['data']);
}
