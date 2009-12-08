<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* inserts the content of an rss feed into a module */
function smarty_function_article($params, &$smarty)
{
	global $tikilib;
	global $dbTiki;
	extract($params);

	if(empty($max)) {
		$max = 99;
	}



	// skip="x,y" will not print Xth and Yth items
	// useful to avoid default first items
	if (!empty($skip) && preg_match('/^\d+(,\d+)*$/', $skip)) {
		$skipped_items = explode(',', $skip);
		$skip = array();
		foreach ($skipped_items as $i) {
			$skip[$i] = 1;
		}
	} else {
		$skip = array();
	}
	$list_articles = $tikilib->list_articles(0, $max, 'publishDate_desc', '', '', '', '', '', '', 1);

	$x = "";

	foreach($list_articles['data'] as $article_data) {
		$x.= "<div class=\"articles\">";
		$x.= "<a href=\"tiki-read_article.php?articleId=".$article_data['articleId']."\" class=\"article\">";
		$x.= $article_data['title']." - ".$tikilib->date_format('%d/%m/%Y',$article_data['publishDate'])."</a></div>\n";     
	}
	echo $x;
}
