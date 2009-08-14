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
require_once 'lib/toolbars/toolbarslib.php';

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

$auto_query_args = array('section', 'comments', 'autoreload');

if( isset($_REQUEST['save'], $_REQUEST['pref']) ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->set_preference( $prefName, $_REQUEST['pref'] );
}

if( isset($_REQUEST['reset']) && $section != 'global' ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->set_preference( $prefName, '');
}

if ( isset($_REQUEST['save_tool'], $_REQUEST['tool_name'])) {	// input from the tool edit form
	Toolbar::saveTool($_REQUEST['tool_name'], $_REQUEST['tool_label'], $_REQUEST['tool_icon'], $_REQUEST['tool_token'], $_REQUEST['tool_syntax']);
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

$qtlist = Toolbar::getList();
$usedqt = array();
$qt_p_list = array();
$qt_w_list = array();
foreach( $current as &$line ) {
	$usedqt = array_merge($usedqt,$line);
}

$customqt = Toolbar::getCustomList();

foreach( $qtlist as $name ) {
	$used = false;
	if (in_array($name, $usedqt) && $name != '-') {
		$used = true;
	}
	$tag = Toolbar::getTag($name);
	if( ! $tag ) {
		continue;
	}
	$wys = strlen($tag->getWysiwygToken()) ? 'qt-wys' : '';
	$wiki = strlen($tag->getWikiHtml('')) ? 'qt-wiki' : '';
	$icon = $tag->getIconHtml();
	if (strpos($name, 'wikiplugin_') !== false) {
		$plug =  'qt-plugin';
		$label = substr($name, 11);
		$qt_p_list[] = $name;
	} else {
		$plug =  '';
		$label = $name;
		$qt_w_list[] = $name;
	}
	$label .= '<input type="hidden" name="token" value="'.$tag->getWysiwygToken().'" />';
	$label .= '<input type="hidden" name="syntax" value="'.$tag->getSyntax().'" />';
	$qtelement[$name] = array( 'name' => $name, 'class' => "toolbar qt-$name $wys $wiki $plug", 'html' => "$icon$label");
}

$nol = 2;
$rowStr = substr(implode(",#row-",range(0,$rowCount)),2);
$fullStr = '#full-list-w,#full-list-p,#full-list-c';

$headerlib->add_jq_onready( <<<JS

var list = \$jq('$fullStr');
var item;

\$jq('$rowStr').sortable({
	connectWith: '$fullStr, .row',
	forcePlaceholderSize: true,
	forceHelperSize: true
});
\$jq('$fullStr').sortable({
	connectWith: '.row, #full-list-c',
	forcePlaceholderSize: true,
	forceHelperSize: true,
	remove: function(event, ui) {	// special handling for separator to allow duplicates
		if (\$jq(ui.item).text() == '-') {
			\$jq(this).prepend(\$jq(ui.item).clone());	// leave a copy at the top of the full list
		}
	},
	receive: function(event, ui) {
		if (\$jq(ui.item).text() == '-') {
			\$jq(this).children().remove('.qt--');				// remove all seps
			\$jq(this).prepend(\$jq(ui.item).clone());			// put one back at the top

		} else if (\$jq(this).attr('id') == 'full-list-c') {	// dropped in custom list
			\$jq(ui.item).dblclick(function() { showToolEditForm(ui.item); });
			\$jq(ui.item).trigger('dblclick');
		}
	}
});
\$jq('#full-list-c').sortable({	// custom tools list
	connectWith: '.lists'
}).children().each(function() {	// add double click action
	\$jq(this).dblclick(function() { showToolEditForm(this); });
});
\$jq('#toolbar_edit_div #cancel_tool').click(function() {
	\$jq('#toolbar_edit_div').hide();
});

showToolEditForm = function(item) {
	\$jq('#toolbar_edit_div').show();
	\$jq('#toolbar_edit_div').css({ left: \$jq(item).offset().left + 10, top:\$jq(item).offset().top + \$jq(item).height() - 4 });
	\$jq('#toolbar_edit_div #tool_name').val(\$jq(item).text());
	\$jq('#toolbar_edit_div #tool_label').val(\$jq(item).children('img').attr('title'));
	\$jq('#toolbar_edit_div #tool_icon').val(\$jq(item).children('img').attr('src'));
	\$jq('#toolbar_edit_div #tool_token').val(\$jq(item).children('input[name=token]').val());
	\$jq('#toolbar_edit_div #tool_syntax').val(\$jq(item).children('input[name=syntax]').val());
}

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

\$jq('#qt_filter_div_w input').click( function () {	// non-plugins	
	\$jq('#full-list-w').children().each( function() {
				
		if (((\$jq('#qt_filter_div_w .qt-wiki-filter').attr('checked') && \$jq(this).hasClass('qt-wiki')) ||
			 (\$jq('#qt_filter_div_w .qt-wys-filter').attr('checked') && \$jq(this).hasClass('qt-wys')))) {
			\$jq(this).show();	
		} else {
			\$jq(this).hide();	
		}
	});
});
\$jq('#qt_filter_div_p input').click( function () {	// non-plugins	
	\$jq('#full-list-p').children().each( function() {
				
		if (((\$jq('#qt_filter_div_p .qt-wiki-filter').attr('checked') && \$jq(this).hasClass('qt-wiki')) ||
			 (\$jq('#qt_filter_div_p .qt-wys-filter').attr('checked') && \$jq(this).hasClass('qt-wys')))) {
			\$jq(this).show();	
		} else {
			\$jq(this).hide();	
		}
	});
});

JS
);
	

$display_w = array_diff($qt_w_list,$usedqt);
$display_p = array_diff($qt_p_list,$usedqt);
$display_c = array_diff($customqt,$usedqt);

$headerlib->add_cssfile('css/admin.css');

if (count($_REQUEST) == 0) {
	$smarty->assign('autoreload', 'on');
} else {
	$smarty->assign('autoreload', isset($_REQUEST['autoreload']) ? $_REQUEST['autoreload'] : '');
}
$smarty->assign('comments', $comments);
$smarty->assign( 'loaded', $section );
$smarty->assign( 'rows', range( 0, $rowCount - 1 ) );
$smarty->assign( 'rowCount', $rowCount );
$smarty->assign( 'sections', $sections );
$smarty->assign_by_ref('qtelement',$qtelement);
$smarty->assign_by_ref('display_w',$display_w);
$smarty->assign_by_ref('display_p',$display_p);
$smarty->assign_by_ref('display_c',$display_c);
//$smarty->assign_by_ref('qtlists',$qtlists);
$smarty->assign_by_ref('current',$current);
$smarty->assign( 'mid', 'tiki-admin_toolbars.tpl' );
$smarty->display( 'tiki.tpl' );

?>
