<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki-Wiki plugin example 
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {NAME(params)}content{NAME}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_sheet_help() {
	return tra("TikiSheet").":<br />~np~{SHEET(id=>x, simple=>n, height=>h)}".tra("Sheet Heading")."{SHEET}~/np~";
}

function wikiplugin_sheet_info() {
	return array(
		'name' => tra('Sheet'),
		'documentation' => tra('PluginSheet'),
		'description' => tra('Displays the content of a spreadsheet in the page.'),
		'prefs' => array( 'wikiplugin_sheet', 'feature_sheet' ),
		'body' => tra('Sheet Heading'),
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Sheet ID'),
				'description' => tra('Internal ID of the TikiSheet.  Either id or url MUST be used.'),
				'filter' => 'digits',
    			'accepted' => 'Sheet ID number',
				'default' => '',
    			'since' => ''
			),
			'url' => array(
				'required' => false,
				'name' => tra('Sheet Url Location'),
				'description' => tra('Internal URL of the Table to use as a spreadsheet.  Either id or url MUST be used.'),
				'filter' => 'url',
    			'accepted' => 'Valid url',
				'default' => '',
    			'since' => '6.0'
			),
			'simple' => array(
				'required' => false,
				'name' => tra('Simple'),
				'description' => tra('Simple table view y/n (Default: n = jquery.sheet view if feature enabled).'),
				'filter' => 'alpha',
    			'accepted' => 'y or n',
				'default' => 'n',
    			'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or percentage. Default value is page width. e.g. "200px" or "100%"'),
				'filter' => 'striptags',
    			'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Page width',
    			'since' => '6.0'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or percentage. Default value is complete spreadsheet height.'),
				'filter' => 'striptags',
    			'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Spreadsheet heigth',
    			'since' => '5.0'
			),
			'editable' => array(
				'required' => false,
				'name' => tra('Editable'),
				'description' => tra('Show edit button. Default \'y\' depending on user\'s permissions.'),
				'filter' => 'alpha',
    			'accepted' => 'y or n',
				'default' => 'y',
    			'since' => '6.0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'subsheets' => array(
				'required' => false,
				'name' => tra('Show subsheets'),
				'description' => tra('y/n. Show multi-sheets. Default \'y\'.'),
				'filter' => 'alpha',
    			'accepted' => 'y or n',
				'default' => 'y',
    			'since' => '6.0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'range' => array(
				'required' => false,
				'name' => tra('Range'),
				'description' => tra('Show a range of cells (or single cell). Default shows all. e.g. "D1:F3" or "e14:e14"'),
				'filter' => 'striptags',
    			'accepted' => 'Cell range, e.g. "D1:F3" or "e14:e14"',
				'default' => 'All cells',
    			'since' => '6.0',
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the containing div.'),
				'filter' => 'text',
    			'accepted' => 'Any valid CSS class',
				'default' => '',
    			'since' => '6.0',
			),
		),
	);
}

function wikiplugin_sheet($data, $params) {
	global $dbTiki, $tiki_p_edit_sheet, $tiki_p_edit, $tiki_p_admin_sheet, $tiki_p_admin, $prefs, $user, $sheetlib, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);
	$style = (isset($height)) ? "height: $height !important;" : '';
	$style .= (isset($width)) ? "width: $width;" : '';
	$urlHeight = (isset($height)) ? "&height=$height" : '';
	$urlHeight .= (isset($width)) ? "&width=$width" : '';
	$editable = isset($editable) && $editable == 'n' ? false : true;
	$subsheets = isset($subsheets) && $subsheets == 'n' ? false : true;
	$class = (isset($class)) ? " $class"  : '';
	
	if( !class_exists( 'TikiSheet' ) )
		require "lib/sheet/grid.php";

	static $index = 0;
	++$index;

	if (!isset($id) && !isset($url)) {
		if( $tiki_p_edit_sheet != 'y' || $tiki_p_edit != 'y' ) {
			return ("<b>missing id parameter for plugin</b><br />");
		} else {
			if( isset( $_POST['create_sheet'], $_POST['index'] ) && $index == $_POST['index'] ) {
				// Create a new sheet and rewrite page
				$sheetId = $sheetlib->replace_sheet( null, tra('New sheet in page: ') . $page, '', $user );
				$page = htmlentities($page);
				$content = htmlentities($data);
				$formId = "form$index";
				return <<<EOF
				~np~
				<form id="$formId" method="post" action="tiki-wikiplugin_edit.php">
				<div>
					<input type="hidden" name="page" value="$page"/>
					<input type="hidden" name="content" value="$data"/>
					<input type="hidden" name="index" value="$index"/>
					<input type="hidden" name="type" value="sheet"/>
					<input type="hidden" name="params[id]" value="$sheetId"/>
				</div>
				</form>
				<script type="text/javascript">
				document.getElementById('$formId').submit();
				</script>
				~/np~
				EOF;
			} else {
				$intro = tra('Incomplete call to plugin: No target sheet.');
				$label = tra('Create New Sheet');
				return <<<EOF
~np~
<form method="post" action="">
	<p>$intro</p>
	<p>
		<input type="submit" name="create_sheet" value="$label"/>
		<input type="hidden" name="index" value="$index"/>
	</p>
</form>
~/np~
EOF;
			}
		}
	}

	if (!empty($id)) {
		$info = $sheetlib->get_sheet_info($id);
		if (empty($info)) {
			return ("<b>missing id parameter for plugin</b><br />");
		}
		$objectperms = Perms::get('sheet', $id);
		if (!$objectperms->view_sheet  && !($user && $info['author'] == $user)) {
			return (tra('Permission denied'));
		}
	}

	// Build required objects
	$sheet = new TikiSheet($id);
	$db = new TikiSheetDatabaseHandler( $id );
	$out = new TikiSheetOutputHandler( $data );

	// Fetch sheet from database
	$sheet->import( $db );
	
	if (!empty($range)) {
		$r = $sheet->setRange($range);
		if (!isset($simple)) {
			$simple = 'y';
		}
	}
	
	// Grab sheet output
	$ret = $sheet->getTableHtml( $subsheets );
	
	if (!isset($simple) || $simple != 'y') {
		global $headerlib;
		$headerlib->add_jq_onready('
			if (typeof ajaxLoadingShow == "function") {
				ajaxLoadingShow("role_main");
			}
			setTimeout (function () {
				$("div.tiki_sheet").tiki("sheet", "",{
					editable:false'. ( isset($url) ? ',
					urlGet: "'.$url.'",
					buildSheet: false': '' ) .'
					});
			}, 0);', 500);

	} else if (preg_match('/^([A-Z]+[0-9]+):\1$/', strtoupper($range))) {
		return $ret;	// return a single cell raw
	}

	$ret = '<div id="tiki_sheet' . $sheet->instance . '" class="tiki_sheet' . $class . '" style="overflow:hidden;' . $style . '">' . $ret . '</div>';
	
	if( $editable && ($objectperms->edit_sheet  || $objectperms->admin_sheet || $tiki_p_admin == 'y')) {
		require_once $smarty->_get_plugin_filepath('function','button');
		
		//If you've given the sheet a url, you can't edit it, disable if not possible
		if (!isset($url)) {
			$button_params = array('_text' => tra("Edit Sheet"), '_script' => "tiki-view_sheets.php?sheetId=$id&parse=edit$urlHeight");
		}
		
		$ret .= smarty_function_button( $button_params, $smarty);
	}
	return '~np~' . $ret . '~/np~';
}
