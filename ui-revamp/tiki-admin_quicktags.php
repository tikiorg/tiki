<?php

require_once 'tiki-setup.php';
require_once 'lib/quicktags/quicktagslib.php';

$access->check_permission('tiki_p_admin');

$init;
foreach( Quicktag::getList() as $name ) {
	$tag = Quicktag::getTag($name);
	if( ! $tag )
		continue;

	$wys = strlen($tag->getWysiwygToken()) ? 'qt-wys' : '';
	$wiki = strlen($tag->getWikiHtml('')) ? 'qt-wiki' : '';
	$init .= <<<JS
item = document.createElement('li');
item.className = 'quicktag qt-$name $wys $wiki';
item.innerHTML = '$name';
list.adopt(item);
JS;
}

$headerlib->add_js( <<<JS
window.addEvent( 'domready', function(event) {
	var item;
	var list = $('full-list');
	$init
	window.quicktags_sortable = new Sortables( $('full-list'), {
		constraint: false,
		clone: true,
		revert: true
	} );

	window.quicktags_sortable.addLists( $('row-0') );
	window.quicktags_sortable.addLists( $('row-1') );
	window.quicktags_sortable.addLists( $('row-2') );
} );
JS
);

$smarty->assign( 'mid', 'tiki-admin_quicktags.tpl' );
$smarty->display( 'tiki.tpl' );

?>
