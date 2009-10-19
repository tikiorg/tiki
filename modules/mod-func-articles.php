<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_articles_info() {
	return array(
		'name' => tra('Articles'),
		'description' => tra('Lists the specified number of published articles in the specified order.'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(
			'showpubl' => array(
				'name' => tra('Show publication time'),
				'description' => tra('If set to "y", article publication times are shown.') . " " . tr('Default: "n".'),
				'filter' => 'word'
			),
			'showcreated' => array(
				'name' => tra('Show creation time'),
				'description' => tra('If set to "y", article creation times are shown.') . " " . tr('Default: "n".'),
				'filter' => 'word'
			),
			'categId' => array(
				'name' => tra('Category filter'),
				'description' => tra('If set to a category identifier, only lists the articles in the specified category.') . " " . tra('Example value: 13.') . " " . tr('Not set by default.'),
				'filter' => 'int'
			),
			'topic' => array(
				'name' => tra('Topic filter (by names)'),
				'description' => tra('If set to a list of article topic names separated by plus signs, only lists the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not listed.') . " " . tra('Example values:') . ' Switching to Tiki, !Switching to Tiki, Tiki upgraded to version 6+Our project is one year old, !Tiki upgraded to version 6+Our project is one year old+Mr. Jones is appointed as CEO.' . " " . tr('Not set by default.')
			),
			'topicId' => array(
				'name' => tra('Topic filter (by identifiers)'),
				'description' => tra('If set to a list of article topic identifiers separated by plus signs, only lists the articles in the specified article topics. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles in the specified article topics are not listed.') . " " . tra('Example values: 13, !13, 1+3, !1+5+7.') . " " . tr('Not set by default.')
			),
			'type' => array(
				'name' => tra('Types filter'),
				'description' => tra('If set to a list of article type names separated by plus signs, only lists the articles of the specified types. If the string is preceded by an exclamation mark ("!"), the effect is reversed, i.e. articles of the specified article types are not listed.') . " " . tra('Example values: Event, !Event, Event+Review, !Event+Classified+Article.') . " " . tr('Not set by default.')
			),
			'sort' => array(
				'name' => tra('Sort'),
				'description' => tra('Specifies how the articles should be sorted.') . " " . tra('Possible values include created and created_asc (equivalent), created_desc, author, rating, topicId, lang and title. Unless "_desc" is specified, the sort is ascending. "created" sorts on article creation date.')  . ' ' . tra('Default value:') . " publishDate_desc",
				'filter' => 'striptags'
			),
			'start' => array(
				'name' => tra('Offset'),
				'description' => tra('If set to an integer, offsets the articles list by the given number. For example, if the module was otherwise set to list the 10 articles most recently published, setting the offset to 10 would make the module list the 11th to 20th articles in descending order of publication time instead.') . " " . tra('Default value:') . " 0",
				'filter' => 'int'
			),
			'more' => array(
				'name' => tra('More'),
				'description' => tra('If set to "y", displays a button labelled "More" that links to a paginated view of the selected articles.') . " " . tr('Default: "n".'),
				'filter' => 'word'
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_articles( $mod_reference, $module_params ) {
	global $smarty, $tikilib, $user;
	
	$urlParams = array(
		'topicId' => 'topic',
		'topic' => 'topicName',
		'categId' => 'categId',
		'type' => 'type',
		'lang' => 'lang',
		'start' => null,
		'sort' => null
	);
	
	foreach ( $urlParams as $p => $v ) {
		if ( isset($$p) ) continue;
		$$p = isset($module_params[$p]) ? $module_params[$p] : '';
	}
	if ( $start == '' ) $start = 0;
	if ( $sort == '' ) $sort = 'publishDate_desc';
	
	$ranking = $tikilib->list_articles($start, $mod_reference['rows'], $sort, '', '', '', $user, $type, $topicId, 'y', $topic, $categId, '', '', $lang);
	
	$smarty->assign_by_ref('urlParams', $urlParams);
	$smarty->assign('modArticles', $ranking["data"]);
	$smarty->assign('more', isset($module_params['more']) ? $module_params['more'] : 'n');
	$smarty->assign('showcreated', isset($module_params['showcreated']) ? $module_params['showcreated'] : 'n');
	$smarty->assign('showpubl', isset($module_params['showpubl']) ? $module_params['showpubl'] : 'n');
}
