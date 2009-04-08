<?php
// Includes rss feed output in a wiki page
// Usage:
// {RSS(id=>feedId,max=>3,date=>1,author=>1,desc=>1)}{RSS}
//

function wikiplugin_rss_help() {
	return tra("~np~{~/np~RSS(id=>feedId:feedId2,max=>3,date=>1,desc=>1,author=>1)}{RSS} Insert rss feed output into a wikipage");
}

function wikiplugin_rss_info() {
	return array(
		'name' => tra('RSS Feed'),
		'documentation' => 'PluginRSS',
		'description' => tra('Inserts an RSS feed output.'),
		'prefs' => array( 'wikiplugin_rss' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('IDs'),
				'description' => tra('List of feed IDs separated by colons. ex: feedId:feedId2'),
			),
			'max' => array(
				'required' => false,
				'name' => tra('Result Count'),
				'description' => tra('Amount of results displayed.'),
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => '0|1',
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => '0|1|max length',
			),
			'author' => array(
				'required' => false,
				'name' => tra('Author'),
				'description' => '0|1',
			),
		),
	);
}

function rss_sort($a,$b) {
	if (isset($a["pubDate"])) {
	$datea=strtotime($a["pubDate"]);
	} else {
		$datea=time();
	}
	if (isset($b["pubDate"])) {
		$dateb=strtotime($b["pubDate"]);
	} else {
		$dateb=time();
	}	
	
	if ($datea==$dateb) {
		return 0;
	} elseif ($datea>$dateb) {
		return -1;
	} else {
		return 1;
	}
}

function wikiplugin_rss($data,$params) {
	global $smarty;
	global $tikilib;
	global $dbTiki;
	global $rsslib;

	if ( ! isset($rsslib) ) {
		include_once('lib/rss/rsslib.php');
	}

	extract($params,EXTR_SKIP);

	if ( ! isset($max) ) { $max = '10'; }
	if ( ! isset($id) ) { return tra('You need to specify a RSS Id'); }
	if ( ! isset($date) ) { $date = 0; }
	if ( ! isset($desc) ) { $desc = 0; }
	if ( ! isset($author) ) { $author = 0; }

	$ids = explode(':', $id);

	$repl = '';		
	$items = array();

	$filter = new DeclFilter;
	$filter->addStaticKeyFilters( array(
		'link' => 'url',
		'title' => 'striptags',
		'author' => 'striptags',
		'pubDate' => 'striptags',
		'description' => 'striptags',
	) );

	foreach ( $ids as $val ) {
		if ( ! ($rssdata = $rsslib->get_rss_module_content($val)) ) {
			$repl = tra('RSS Id incorrect:').' '.$val;
		}
		$itemsrss = $rsslib->parse_rss_data($rssdata, $val, $rssdata);

		foreach($itemsrss as & $item) {
			foreach( $item as &$v ) {
				$v = TikiLib::htmldecode($v);
			}
			$item = $filter->filter($item);

			if( $desc > 1 && strlen($item['description']) > $desc ) {
				$item['description'] = substr($item['description'], 0, $desc ) . ' [...]';
			}
		}

		$items = array_merge($items, $itemsrss);		
	}
 
	$title = null;
	if ( isset($items[0]) && $items[0]['isTitle'] == 'y' ) {
		$title = array_shift($items);
	}

	// No need to waste time sorting with only one feed
	if( count( $ids ) > 1 ) {
		usort($items, 'rss_sort');
	}

	$items = array_slice($items, 0, $max);

	if ( count($items) < $max ) $max = count($items);

	global $smarty;
	$smarty->assign('title', $title);
	$smarty->assign('items', $items);
	$smarty->assign('showdate', $date > 0);
	$smarty->assign('showdesc', $desc > 0);
	$smarty->assign('showauthor', $author > 0);
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_rss.tpl' ) . '~/np~';
}

?>
