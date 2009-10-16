<?php
//$Id$
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_article_topics_info() {
	return array(
		'name' => tra('Article Topics'),
		'description' => tra('Lists all article topics with links to their articles.'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(),
		'common_params' => array('nonums')
	);
}

function module_article_topics( $mod_reference, $module_params ) {
	global $smarty;
	global $artlib; include_once('lib/articles/artlib.php');
	
	$listTopics = $artlib->list_topics();
	$smarty->assign('listTopics', $listTopics);
}
