<?php

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'save' => 'alpha',
		'load' => 'alpha',
		'pref' => 'striptags',
		'section' => 'striptags',
	),
) );

$auto_query_args = array('section', 'comments', 'autoreload');

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

$sections = array( 'global', 'wiki page', 'trackers', 'blogs', 'calendar', 'cms', 'faqs', 'newsletters', 'forums', 'maps', 'admin');

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

if( isset($_REQUEST['save'], $_REQUEST['pref']) ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->set_preference( $prefName, $_REQUEST['pref'] );
}

if( isset($_REQUEST['reset']) && $section != 'global' ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->delete_preference( $prefName);
}

if( isset($_REQUEST['reset_global']) && $section == 'global' ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->delete_preference( $prefName);
}

if ( !empty($_REQUEST['save_tool']) && !empty($_REQUEST['tool_name'])) {	// input from the tool edit form
	Toolbar::saveTool($_REQUEST['tool_name'], $_REQUEST['tool_label'], $_REQUEST['tool_icon'], $_REQUEST['tool_token'], $_REQUEST['tool_syntax'], $_REQUEST['tool_type'], $_REQUEST['tool_plugin']);
}

$current = $tikilib->get_preference( 'toolbar_' . $section . ($comments ? '_comments' : '') );

if ( !empty($_REQUEST['delete_tool']) && !empty($_REQUEST['tool_name'])) {	// input from the tool edit form
	Toolbar::deleteTool($_REQUEST['tool_name']);
	if (strpos($_REQUEST['tool_name'], $current) !== false) {
		$current = str_replace($_REQUEST['tool_name'], '', $current);
		$current = str_replace(',,', ',', $current);
		$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
		$tikilib->set_preference( $prefName, $current );
	}
}

if (!empty($current)) {
	$current = preg_replace( '/\s+/', '', $current );
	$current = trim( $current, '/' );
	$current = explode( '/', $current );
	$loadedRows = count($current);
	foreach( $current as &$line ) {
		$bits = explode( '|', $line );
		$line = array();
		foreach($bits as $bit) {
			$line[] = explode( ',', $bit );
		}
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
	foreach($line as $bit) {
		$usedqt = array_merge($usedqt,$bit);
	}
}

$customqt = Toolbar::getCustomList();

foreach( $qtlist as $name ) {

	$tag = Toolbar::getTag($name);
	if( ! $tag ) {
		continue;
	}
	$wys = strlen($tag->getWysiwygToken()) ? 'qt-wys' : '';
	$wiki = strlen($tag->getWikiHtml('')) ? 'qt-wiki' : '';
	$cust = Toolbar::isCustomTool($name) ? 'qt-custom' : '';
	$icon = $tag->getIconHtml();
	if (strpos($name, 'wikiplugin_') !== false) {
		$plug =  'qt-plugin';
		$label = substr($name, 11);
		$qt_p_list[] = $name;
	} else {
		$plug =  '';
		$label = $name;
		if (empty($cust)) {
			$qt_w_list[] = $name;
		}
	}
	$label .= '<input type="hidden" name="token" value="'.$tag->getWysiwygToken().'" />';
	$label .= '<input type="hidden" name="syntax" value="'.$tag->getSyntax().'" />';
	$label .= '<input type="hidden" name="type" value="'.$tag->getType().'" />';
	if ($tag->getType() == 'Wikiplugin') {
		$label .= '<input type="hidden" name="plugin" value="'.$tag->getPluginName().'" />';
	}
	$qtelement[$name] = array( 'name' => $name, 'class' => "toolbar qt-$name $wys $wiki $plug $cust", 'html' => "$icon<span>$label</span>");
}

$nol = 2;
$rowStr = substr(implode(",#row-",range(0,$rowCount)),2);
$fullStr = '#full-list-w,#full-list-p,#full-list-c';

$delete_text = tra('Are you sure you want to delete this custom tool?');

$headerlib->add_jq_onready( <<<JS

var list = \$jq('$fullStr');
var item;

\$jq('$rowStr').sortable({
	connectWith: '$fullStr, .row',
	forcePlaceholderSize: true,
	forceHelperSize: true,
	receive: function(event, ui) {
		var x = $jq(ui.item).parent().offset().left + $jq(ui.item).parent().width() - ui.offset.left;
		//alert(x);
		if (x < 32) {
			\$jq(ui.item).css("float", "right");
		} else {
			\$jq(ui.item).css("float", "left");
		}
	}
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
\$jq('.qt-custom').dblclick(function() { showToolEditForm(this); });

//\$jq('#toolbar_edit_div #cancel_tool').click(function() {
//	\$jq('#toolbar_edit_div').hide();
//});
//\$jq('#toolbar_edit_div form').submit(function() {
//});

// show edit form dialogue
showToolEditForm = function(item) {

	//\$jq('#toolbar_edit_div').show();
	//\$jq('#toolbar_edit_div').css({ left: \$jq(item).offset().left + 10, top:\$jq(item).offset().top + \$jq(item).height() - 4 });
	\$jq('#toolbar_edit_div #tool_name').val(\$jq(item).text()); //.attr('disabled','disabled');
	\$jq('#toolbar_edit_div #tool_label').val(\$jq(item).children('img').attr('title'));
	\$jq('#toolbar_edit_div #tool_icon').val(\$jq(item).children('img').attr('src'));
	\$jq('#toolbar_edit_div #tool_token').val(\$jq(item).children('input[name=token]').val());
	\$jq('#toolbar_edit_div #tool_syntax').val(\$jq(item).children('input[name=syntax]').val());
	\$jq('#toolbar_edit_div #tool_type').val(\$jq(item).children('input[name=type]').val());
	if (\$jq(item).children('input[name=type]').val() == 'Wikiplugin') {
		\$jq('#toolbar_edit_div #tool_plugin').val(\$jq(item).children('input[name=plugin]').val());
	} else {
		\$jq('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled');
	}

	\$jq('#toolbar_edit_div').dialog('open');
}
// handle plugin select on edit dialogue
\$jq('#toolbar_edit_div #tool_type').change( function () {
	if (\$jq('#toolbar_edit_div #tool_type').val() == 'Wikiplugin') {
		\$jq('#toolbar_edit_div #tool_plugin').removeAttr('disabled');
	} else {
		\$jq('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled').val("");
	}
});

\$jq("#toolbar_edit_div").dialog({
	bgiframe: true,
	autoOpen: false,
//	height: 300,
	modal: true,
	buttons: {
		Cancel: function() {
			\$jq(this).dialog('close');
		},
		'Save': function() {
			var bValid = true;
//			allFields.removeClass('ui-state-error');
//
//			bValid = bValid && checkLength(\$jq('#toolbar_edit_div #tool_name'),"name",2,16);
//			bValid = bValid && checkLength(\$jq('#toolbar_edit_div #tool_label'),"label",1,80);
			
			if (bValid) {
				\$jq("#toolbar_edit_div #save_tool").val('Save');
				\$jq("#toolbar_edit_div form").submit();
			}
			\$jq(this).dialog('close');
		},
		Delete: function() {
			if (confirm("$delete_text")) {
				\$jq("#toolbar_edit_div #delete_tool").val('Delete');
				\$jq("#toolbar_edit_div form").submit();
			}
			\$jq(this).dialog('close');
		}
	},
	close: function() {
		//allFields.val('').removeClass('ui-state-error');
	}
});

// save toolbars
saveRows = function() {
	var lists = [];
	var ser = \$jq('.row').map(function(){				/* do this on everything of class 'row' */
		var right_section = false;
		return \$jq(this).children().map(function(){	/* do this on each child node */
			var text = "";
			if (\$jq(this).text() == "help") {
				var a = 1;
			}
			if ( !right_section && \$jq(this).css("float") == "right") {
				text = "|";
				right_section = true;
			}
			if (\$jq(this).hasClass('qt-plugin')) { text += 'wikiplugin_'; }
			text += \$jq(this).text();
			return text;
		}).get().join(",").replace(",|", "|");								/* put commas inbetween */
	});
	if (typeof(ser) == 'object' && ser.length > 1) {
		ser = \$jq.makeArray(ser).join('/');			// row separators
	} else {
		ser = ser[0];
	}
	\$jq('#qt-form-field').val(ser.replace(',,', ','));
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
if (!in_array('-', $display_w)) {
	array_unshift($display_w, '-');
}
$display_p = array_diff($qt_p_list,$usedqt);
$display_c = array_diff($customqt,$usedqt);

$headerlib->add_cssfile('css/admin.css');

if (count($_REQUEST) == 0) {
	$smarty->assign('autoreload', 'on');
} else {
	$smarty->assign('autoreload', isset($_REQUEST['autoreload']) ? $_REQUEST['autoreload'] : '');
}

$plugins = array();
foreach($tikilib->plugin_get_list() as $name) {
	$info = $tikilib->plugin_info($name);
	if (isset($info['prefs']) && is_array($info['prefs']) && count($info['prefs']) > 0) $plugins[$name] = $info;
}
$smarty->assign('plugins', $plugins);

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
