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
				'description' => '0|1|2',
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
	if ($datea<$dateb) {
		return true;
	} else {
		return false;
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
	foreach ( $ids as $val ) {
		if ( ! ($rssdata = $rsslib->get_rss_module_content($val)) ) {
			$repl = tra('RSS Id incorrect:').' '.$val;
		}
		$itemsrss = $rsslib->parse_rss_data($rssdata, $val, $rssdata);

		$items = array_merge($items, $itemsrss);		
	}
 
	if ( isset($items[0]) && $items[0]['isTitle'] == 'y' ) {
		$repl .= '<div class="rsstitle"><a target="_blank" href="'.$items[0]['link'].'">'.TikiLib::htmldecode($items[0]['title']).'</a></div>'; 
		$items = array_slice($items, 1);
	}

	usort($items, 'rss_sort');
	if ( count($ids) > 1 ) {
		$items = array_slice($items, count($ids));
	}

	if ( count($items) < $max ) $max = count($items);

	$repl .= '<ul class="rsslist">';
	for ( $j = 0 ; $j < $max ; $j++ ) {

		$repl .= '<li  class="rssitem"><a target="_blank" href="'.$items[$j]['link'].'">'.TikiLib::htmldecode($items[$j]['title']).'</a>';

		if ( $author == 1 || $date == 1 ) {
			$repl_author = '';
			if ( $author == 1 && isset($items[$j]['author']) && $items[$j]['author'] <> '' ) {
				$repl_author .= $items[$j]['author'];
				if ( $date == 1 ) {
					$repl_author .= ', ';
				}
			}
			if ( $date == 1 && isset($items[$j]['pubDate']) && $items[$j]['pubDate'] <> '' ) {
				$repl_author .= '<span class="rssdate">'.$items[$j]['pubDate'].'</span>';
			}
			if ( $repl_author != '' ) {
				$repl .= '&nbsp;&nbsp;&nbsp;('.$repl_author.')';
			}
		}

		if ( $desc == 1 && !empty($items[$j]['description'])) {
			$repl .= '<div class="rssdescription">'.TikiLib::htmldecode($items[$j]['description']).'</div>';
		}

		if ( $desc > 1 ) {
			$repl .= '<div class="rssdescription">'.substr(strip_tags(TikiLib::htmldecode($items[$j]['description'])),0,$desc).' <a href="'.$items[$j]['link'].'">[...]</a></div>';
		}
		$repl .= '</li>';
	}
	$repl .= '</ul>';
	return '~np~'.$repl.'~/np~';
}

?>
