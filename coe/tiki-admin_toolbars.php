<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
	if ($prefs['feature_use_minified_scripts'] == 'y') {
		$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/minified/jquery-ui.min.js');
	} else {
		$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/jquery-ui.js');
	}
	$headerlib->add_cssfile('lib/jquery/jquery-ui/themes/'.$prefs['feature_jquery_ui_theme'].'/jquery-ui.css');
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
	require_once($smarty->_get_plugin_filepath('function', 'query'));
	header('location: ?'. smarty_function_query(array('_urlencode'=>'n'), $smarty));
}

if( isset($_REQUEST['reset_global']) && $section == 'global' ) {
	$prefName = 'toolbar_' . $section . ($comments ? '_comments' : '');
	$tikilib->delete_preference( $prefName);
	require_once($smarty->_get_plugin_filepath('function', 'query'));
	header('location: ?'. smarty_function_query(array('_urlencode'=>'n'), $smarty));
}

if ( !empty($_REQUEST['save_tool']) && !empty($_REQUEST['tool_name'])) {	// input from the tool edit form
	Toolbar::saveTool($_REQUEST['tool_name'], $_REQUEST['tool_label'], $_REQUEST['tool_icon'], $_REQUEST['tool_token'], $_REQUEST['tool_syntax'], $_REQUEST['tool_type'], $_REQUEST['tool_plugin']);
}

$current = $tikilib->get_preference( 'toolbar_' . $section . ($comments ? '_comments' : '') );
if (empty($current)) {
	$current = $tikilib->get_preference( 'toolbar_global' . ($comments ? '_comments' : '') );
	$smarty->assign('not_global', false);
} else {
	$smarty->assign('not_global', true);
}
$smarty->assign('not_default', false);
if ($section == 'global') {
	global $cachelib;
	if( isset($cachelib) && $cachelib->isCached("tiki_default_preferences_cache") ) {
		$defprefs = unserialize( $cachelib->getCached("tiki_default_preferences_cache") );
		if ( $defprefs !== false ) {
			if ($defprefs['toolbar_global' . ($comments ? '_comments' : '')] != $current) {
				$smarty->assign('not_default', true);
			}
		}
	}
}

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
	$avail = $tag->isAccessible() ? '' : 'qt-noaccess';
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
	$qtelement[$name] = array( 'name' => $name, 'class' => "toolbar qt-$name $wys $wiki $plug $cust $avail", 'html' => "$icon<span>$label</span>");
}

$headerlib->add_js( "var toolbarsadmin_rowStr = '" . substr(implode(",#row-",range(0,$rowCount)),2) . "'
var toolbarsadmin_fullStr = '#full-list-w,#full-list-p,#full-list-c';
var toolbarsadmin_delete_text = '" . tra('Are you sure you want to delete this custom tool?') . "'\n");

$headerlib->add_jsfile('lib/toolbars/tiki-admin_toolbars.js');

$display_w = array_diff($qt_w_list,$usedqt);
if (!in_array('-', $display_w)) {
	array_unshift($display_w, '-');
}
$display_p = array_diff($qt_p_list,$usedqt);
$display_c = array_diff($customqt,$usedqt);

sort($display_c);
sort($display_p);
sort($display_w);

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
