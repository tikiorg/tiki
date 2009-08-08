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

if ($prefs['javascript_enabled'] != 'y') {
	$smarty->assign('msg', tra("JavaScript is required for this page"));
	$smarty->display("error.tpl");
	die;
}


if ($prefs['feature_jquery_ui'] != 'y') {
	$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/minified/jquery-ui.min.js');
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

$qtlist = Quicktag::getList();
$usedqt = array();
foreach( $current as &$line ) {
	$usedqt = array_merge($usedqt,$line);
}

foreach( $qtlist as $name ) {
	$used = false;
	if (in_array($name, $usedqt) && $name != '-') {
		$used = true;
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
	$qtelement[$name] = array( 'name' => $name, 'class' => "quicktag qt-$name $wys $wiki $plug", 'html' => "$icon$label" );
}

$rowStr = substr(implode(",#row-",range(0,$rowCount)),2);

$headerlib->add_jq_onready( <<<JS

var list = \$jq('#full-list');
var item;

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

//window.quicktags_sortable = Object();
//window.quicktags_sortable.saveRows = function() {
saveRows = function() {
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
	

$headerlib->add_cssfile('css/admin.css');

$smarty->assign('comments', $comments);
$smarty->assign( 'loaded', $section );
$smarty->assign( 'rows', range( 0, $rowCount - 1 ) );
$smarty->assign( 'sections', $sections );
$smarty->assign_by_ref('qtelement',$qtelement);
$smarty->assign_by_ref('qtlist',$qtlist);
$smarty->assign_by_ref('displayedqt',array_diff($qtlist,$usedqt));
$smarty->assign_by_ref('current',$current);
$smarty->assign( 'mid', 'tiki-admin_quicktags.tpl' );
$smarty->display( 'tiki.tpl' );

?>
