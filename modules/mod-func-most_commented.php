<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_most_commented_info()  {
	return array(
		'name' => tra('Most commented'),
		'description' => tra('Displays the most commented objects. Can be used for Wiki Pages, Blog Posts or Articles'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(
			'objectType' => array(
				'name' => tra('Content Type'),
				'description' => tra('used to identify which type of content you want. Default is Wiki Pages, Options are: wiki, blog, article') 
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_most_commented( $mod_reference, $module_params ) {
	global $smarty;
	global $dbTiki;
	global $commentslib;
	if(!isset($commentslib)){
		include_once ('lib/commentslib.php');
		$commentslib = new Comments($dbTiki);
	}
	$type = 'wiki';
	if(isset($module_params['objectType'])){
		$type = $module_params['objectType'];
		if($type != 'article' && $type != 'blog' && $type != 'wiki'){
			//If parameter is not properly set then default to wiki
			$type = 'wiki';
		}
	}
	
	$lang = '';
	if(isset($module_params['lang'])){
		$lang = $module_params['lang'];
	}
	
	
	$result = $commentslib->order_comments_by_count($type, $lang, $mod_reference['rows']);
	$smarty->assign('modMostCommented', $result['data']);
	$smarty->assign('modContentType', $type);
	$smarty->assign('nonums', $module_params['nonums']);
	
}
