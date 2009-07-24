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

$sections = array( 'global', 'wiki page' );

if( isset($_REQUEST['section'])
	&& in_array($_REQUEST['section'], $sections) ) {

	$section = $_REQUEST['section'];
} else {
	$section = reset($sections);
}

$auto_query_args = array('section');

if( isset($_REQUEST['save'], $_REQUEST['pref']) ) {
	$prefName = 'toolbar_' . $section;
	$tikilib->set_preference( $prefName, $_REQUEST['pref'] );
}

$current = $tikilib->get_preference( 'toolbar_' . $section );
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
	$tag = Quicktag::getTag($name);
	if( ! $tag )
		continue;

	$wys = strlen($tag->getWysiwygToken()) ? 'qt-wys' : '';
	$wiki = strlen($tag->getWikiHtml('')) ? 'qt-wiki' : '';
	$icon = $tag->getIconHtml();
	$map[$name] = <<<JS
item = document.createElement('li');
item.className = 'quicktag qt-$name $wys $wiki';
item.innerHTML = '$icon$name';
JS;

	$init .= $map[$name];
	$init .= 'list.adopt(item);';
}

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

$headerlib->add_cssfile('css/admin.css');

$smarty->assign( 'loaded', $section );
$smarty->assign( 'rows', range( 0, $rowCount - 1 ) );
$smarty->assign( 'sections', $sections );
$smarty->assign( 'mid', 'tiki-admin_quicktags.tpl' );
$smarty->display( 'tiki.tpl' );

?>
