<?php
// Includes a wiki page in another
// Usage:
// {ARTICLES(max=>3)}{ARTICLES}
//
// Damian added the following parameter
// topic=>topicId

function wikiplugin_articles_help() {
	return tra("Insert articles into a wikipage");
}

function wikiplugin_articles($data,$params) {
	global $smarty;
	global $tikilib;
	global $feature_articles;
	global $tiki_p_read_article;
	extract($params);
	if (($feature_articles !=  'y') || ($tiki_p_read_article != 'y')) {

//		the feature is disabled or the user can't read articles
		return("");
	}
	if(!isset($max)) {$max='3';}
	if(!isset($topic)) {
	  $topic='';
        } else {
	  $topic = $tikilib->fetchtopicId($topic);
	}
	
	$now = date("U");
	
	$listpages = $tikilib->list_articles(0, $max, 'publishDate_desc', '', $now, 'admin', '', $topic);
  
	for ($i = 0; $i < count($listpages["data"]); $i++) {
		$listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data($listpages["data"][$i]["heading"]);
		//print_r($listpages["data"][$i]['title']);
	}
	
	// If there're more records then assign next_offset
	$smarty->assign_by_ref('listpages', $listpages["data"]);
	
	return "~np~ ".$smarty->fetch('tiki-view_articles.tpl')." ~/np~";
}
?>
