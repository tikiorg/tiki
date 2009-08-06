<?php

/*$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'save' => 'alpha',
		'load' => 'alpha',
		'pref' => 'striptags',
		'section' => 'striptags',
	),
	'catchAllUnset' => null,
) );*/
/* disabled for now, was stopping js detect */

require_once 'tiki-setup.php';
require_once 'lib/quicktags/quicktagslib.php';

$access->check_permission('tiki_p_admin');

if ($prefs['feature_jquery'] == 'y') {
	if ($prefs['feature_jquery_ui'] != 'y' && $prefs['feature_mootools'] != 'y') {
		$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/minified/jquery-ui.min.js');
	}
} else if ($prefs['feature_mootools'] != 'y') {	// jquery not assumed as enabled (yet)
	include_once('lib/smarty_tiki/block.self_link.php');
	$button = smarty_block_self_link(array('_script'=>'tiki-admin.php', 'page'=>'features', 'cookietab'=>'5', '_icon'=>'arrow_right'), tra('Enable feature'), $smarty);
	$smarty->assign('msg', tra("This feature is disabled"). ' ' . $button . ": feature_jquery");
	$smarty->display("error.tpl");
	die;
}

$sections = array( 'global', 'wiki page', 'trackers', 'blogs', 'calendar', 'cms', 'faqs', 'newsletters', 'forums', 'maps');

if( isset($_REQUEST['section']) && in_array($_REQUEST['section'], $sections) ) {
	$section = $_REQUEST['section'];
} else {
	$section = reset($sections);
}
if( isset($_REQUEST['comments']) && $_REQUEST['comments'] == 'on') {
	$comments = true;
} else {
	$comments = false;
}

$auto_query_args = array('section', 'comments');

if( isset($_REQUEST['save'], $_REQUEST['pref']) ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->set_preference( $prefName, $_REQUEST['pref'] );
}

$current = $tikilib->get_preference( 'toolbar_' . $section . ($comments ? '_comments' : '') );
if (!empty($current)) {
	$current = preg_replace( '/\s+/', '', $current );
	$current = trim( $current, '/' );
	$current = explode( '/', $current );
	$loadedRows = count($current);
	foreach( $current as & $line ) {
		$line = explode( ',', $line );
	}

	$rowCount = max($loadedRows, 1) + 1;
} else {
	$rowCount = 1;
}
$init = '';
$setup = '';
$map = array();
foreach( Quicktag::getList() as $name ) {
	$used = false;
	foreach( $current as & $line ) {
		if (in_array($name, $line) && $name != '-') {
			$used = true;
			break;
		}
	}
	$tag = Quicktag::getTag($name);
	if( ! $tag ) {
		continue;
	}
	$wys = strlen($tag->getWysiwygToken()) ? 'qt-wys' : '';
	$wiki = strlen($tag->getWikiHtml('')) ? 'qt-wiki' : '';
	if (strpos($name, 'wikiplugin_') !== false) {
		$plug =  'qt-plugin';
		$label = substr($name, 11);
	} else {
		$plug =  '';
		$label = $name;
	}
	$icon = $tag->getIconHtml();
	$map[$name] = <<<JS
item = document.createElement('li');
item.className = 'quicktag qt-$name $wys $wiki $plug';
item.innerHTML = '$icon$label';
JS;
	if (!$used) {

		$init .= $map[$name];
		if ($prefs['feature_jquery'] == 'y') {
			$init .= "list.append(item);\n";
		} else {
			$init .= 'list.adopt(item);';
		}
	}
}

if ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_ui'] == 'y') {	// would be nice to lose all this soon :)

	foreach( $current as $k => $l ) {
		foreach( $l as $name ) {
			if( isset($map[$name]) ) {
				$init .= $map[$name];
				$init .= "\$jq('#row-$k').append(item);";
			}
		}
	}
	$rowStr = '';
	for( $i = 0; $rowCount > $i; ++$i ) {
		$rowStr .= !empty($rowStr) && $i < $rowCount ? ',' : '';
		$rowStr .= "#row-$i";
	}
	$headerlib->add_jq_onready( <<<JS

var list = \$jq('#full-list');
var item;
$init

\$jq('$rowStr').sortable({
	connectWith: '#full-list, .row',
	forcePlaceholderSize: true,
	forceHelperSize: true
});
\$jq('#full-list').sortable({
	connectWith: '.row',
	forcePlaceholderSize: true,
	forceHelperSize: true,
	remove: function(event, ui) {	// special handling for separator to allow duplicates
		if (\$jq(ui.item).text() == '-') {
			\$jq(this).prepend(\$jq(ui.item).clone());	// leave a copy at the top of the full list
		}
	},
	receive: function(event, ui) {
		if (\$jq(ui.item).text() == '-') {
			\$jq(this).children().remove('.qt--');		// remove all seps
			\$jq(this).prepend(\$jq(ui.item).clone());	// put one back at the top
		}
	}
}); 							//.disableSelection();

window.quicktags_sortable = Object();
window.quicktags_sortable.saveRows = function() {
	var lists = [];
	var ser = \$jq('.row').map(function(){				/* do this on everything of class 'row' */
		return \$jq(this).children().map(function(){	/* do this on each child node */
			return \$jq(this).hasClass('qt-plugin') ?	/* put back label prefix for plugins */
				'wikiplugin_' + \$jq(this).text() : \$jq(this).text();
		}).get().join(",")								/* put commas inbetween */
	});
	if (typeof(ser) == 'object' && ser.length > 1) {
		ser = \$jq.makeArray(ser).join('/');			// row separators
	} else {
		ser = ser[0];
	}
	\$jq('#qt-form-field').val(ser);
}

\$jq('.qt-filter').click( function () {

	var showwiki = \$jq('#qt-wiki-filter').attr('checked');
	var showwys = \$jq('#qt-wys-filter').attr('checked');
	var showplugin = \$jq('#qt-plugin-filter').attr('checked');
	
	\$jq('#full-list').children().hide();		// reset
	
	\$jq('#full-list').children().each( function() {
		
		var haswiki = \$jq(this).hasClass('qt-wiki');
		var haswys = \$jq(this).hasClass('qt-wys');
		var hasplugin = \$jq(this).hasClass('qt-plugin');
		
		if (showplugin) {
			if ((showwiki && haswiki) || (showwys && haswys) || (!showwys && !showwiki && hasplugin)) {
				\$jq(this).show();	
			}
		} else {
			if (!hasplugin && ((showwiki && haswiki) || (showwys && haswys))) {
				\$jq(this).show();	
			}
		}
	});
});

JS
	);
	
}	// end jq

if ($prefs['feature_mootools'] == 'y' && ($prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_ui'] != 'y')) {
	
	foreach( $current as $k => $l ) {
		foreach( $l as $name ) {
			if( isset($map[$name]) ) {
				$init .= $map[$name];
				$init .= "\$('row-$k').adopt(item);";
			}
		}
	}
	
	for( $i = 0; $rowCount > $i; ++$i )
		$setup .= <<<JS
window.quicktags_sortable.addLists( $('row-$i') );
JS;

	$headerlib->add_js( <<<JS
window.addEvent( 'domready', function(event) {
	var item;
	var list = $('full-list');
	$init
	window.quicktags_sortable = new Sortables( $('full-list'), {
		constrain: false,
		clone: true,
		revert: true
	} );

	$setup

	var seri = function(element) {
		if (element.hasChildNodes()) {
			return element.lastChild.nodeValue;
		} else {
			return element.innerHTML;
		}
	};

	window.quicktags_sortable.saveRows = function() {
		window.quicktags_sortable.removeLists($('full-list'));
		var lists = [];
		var ser = window.quicktags_sortable.serialize(false, seri );
		if (typeof(ser[0]) == 'object' && (ser[0] instanceof Array)) {
			for( var i = 0; ser.length > i; ++i )
				lists.push( ser[i].join(',') );
		} else {
			lists.push( ser.join(',') );
		}

		$('qt-form-field').value = lists.join('/');
	}
} );
JS
	);
}	// end if mootools

$headerlib->add_cssfile('css/admin.css');

$smarty->assign('comments', $comments);
$smarty->assign( 'loaded', $section );
$smarty->assign( 'rows', range( 0, $rowCount - 1 ) );
$smarty->assign( 'sections', $sections );
$smarty->assign( 'mid', 'tiki-admin_quicktags.tpl' );
$smarty->display( 'tiki.tpl' );

?>
