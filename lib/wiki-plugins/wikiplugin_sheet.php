<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sheet_info()
{
	return array(
		'name' => tra('Sheet'),
		'documentation' => 'PluginSheet',
		'description' => tra('Display data from a TikiSheet'),
		'prefs' => array( 'wikiplugin_sheet', 'feature_sheet' ),
		'body' => tra('Spreadsheet Heading'),
		'iconname' => 'table',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Spreadsheet ID'),
				'description' => tr('Internal ID of the TikiSheet. Either %0id%1 or %0url%1 is required.', '<code>', '</code>'),
				'filter' => 'digits',
				'accepted' => 'Spreadsheet ID number',
				'default' => '',
				'since' => '1',
				'profile_reference' => 'sheet',
			),
			'url' => array(
				'required' => false,
				'name' => tra('Sheet Url Location'),
				'description' => tr('Internal URL of the Table to use as a spreadsheet. Either %0id%1 or %0url%1 is
					required.', '<code>', '</code>'),
				'filter' => 'url',
				'default' => '',
				'since' => '6.0'
			),
			'simple' => array(
				'required' => false,
				'name' => tra('Simple'),
				'description' => tr('Show a simple table view (Default: %0 = jquery.sheet view if feature enabled).',
					'<code>n</code>'),
				'filter' => 'alpha',
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
				'description' => tr('Width in pixels or percentage. Default value is page width, for example, %0200px%1 or
					%0100%%1', '<code>', '</code>'),
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by %).',
				'default' => 'Page width',
				'since' => '6.0'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or percentage. Default value is complete spreadsheet height.'),
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by %).',
				'default' => 'Spreadsheet height',
				'since' => '5.0'
			),
			'editable' => array(
				'required' => false,
				'name' => tra('Editable'),
				'description' => tra('Show edit button. Default is to show depending on user\'s permissions.'),
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
				'description' => tra('Show multi-sheets (default is to show)'),
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
				'description' => tr('Show a range of cells (or single cell). Default shows all. e.g. %0D1:F3%1 or
					%0e14:e14%1', '<code>', '</code>'),
				'filter' => 'text',
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

function wikiplugin_sheet($data, $params)
{
	global $tiki_p_edit_sheet, $tiki_p_edit, $tiki_p_admin_sheet, $tiki_p_admin, $prefs, $user, $page;
	extract($params, EXTR_SKIP);
	$style = (isset($height)) ? "height: $height !important;" : '';
	$style .= (isset($width)) ? "width: $width;" : '';
//	$urlHeight = (isset($height)) ? "&height=$height" : '';
//	$urlHeight .= (isset($width)) ? "&width=$width" : '';
	$urlHeight = (isset($height)) ? "&height=100" : ''; // not setting any height or width in the sheet params created for me the literal '...&height=100%&...' or '...&width=100%&...' in the url with a 400 error (bad request). Hardcoding to 100 (instead of 100%) to avoid this error until a better fix is found
	$urlHeight .= (isset($width)) ? "&width=100" : ''; // not setting any height or width in the sheet params created for me the literal '...&height=100%&...' or '...&width=100%&...' in the url with a 400 error (bad request). Hardcoding to 100 (instead of 100%) to avoid this error until a better fix is found
	$editable = isset($editable) && $editable == 'n' ? false : true;
	$subsheets = isset($subsheets) && $subsheets == 'n' ? false : true;
	$class = (isset($class)) ? " $class"  : '';

	$sheetlib = TikiLib::lib("sheet");
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');

	static $index = 0;
	++$index;

	if (empty($id) && empty($url)) {
		if ( $tiki_p_edit_sheet != 'y' || $tiki_p_edit != 'y' ) {
			return ("<b>missing id parameter for plugin</b><br />");
		} else {
			if ( isset( $_POST['create_sheet'], $_POST['index'] ) && $index == $_POST['index'] ) {
				// Create a new sheet and rewrite page
				$sheetId = $sheetlib->replace_sheet(null, tra('New sheet in page: ') . $page, '', $user);
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
				$label = tra('Create New Sheet');
				return <<<EOF
~np~
<form method="post" action="">
	<p>
		<input type="submit" name="create_sheet" class="btn btn-default" value="$label"/>
		<input type="hidden" name="index" value="$index"/>
	</p>
</form>
~/np~
EOF;
			}
		}
	}

	$sheet = new TikiSheet();

	if (empty($url)) {
		$info;
		if (!empty($id)) {
			$info = $sheetlib->get_sheet_info($id);
		}

		if (empty($info)) {
			return tra("Error loading spreadsheet");
		}

		$objectperms = Perms::get('sheet', $id);
		if (!$objectperms->view_sheet  && !($user && $info['author'] == $user)) {
			return (tra('Permission denied'));
		}

		// Build required objects
		$db = new TikiSheetDatabaseHandler($id);
		//$out = new TikiSheetOutputHandler($data);

		// Fetch sheet from database
		$sheet->import($db);

	} else {
		if (!isset($simple)) {
			$simple = 'y';
		}
	}

	$calcOff = '';
	if (!empty($range)) {
		$sheet->setRange($range);
		$calcOff = ',calcOff: true';
	}

	// Grab sheet output
	if (isset($url)) {
		$file = file_get_contents($url);
		$pathInfo = pathinfo($url);
		if ($pathInfo['extension'] == 'csv') {
			$handler = new TikiSheetCSVHandler($url);
			$grid = new TikiSheet();
			$grid->import($handler);
			$ret = $grid->getTableHtml(true, null, false);

		} else {
			$ret = file_get_contents($url);
		}
	} else {
		$ret = ($sheet->getTableHtml($subsheets));
	}

	if (strpos($ret, '<table ') === false) {
		return '~np~' . $ret . '~/np~';	// return a single cell raw
	}

	if (!isset($simple) || $simple != 'y') {
		$headerlib = TikiLib::lib('header');
		$sheetlib->setup_jquery_sheet();
		$headerlib->add_jq_onready(
			'$("div.tiki_sheet").each(function() {
				$(this).sheet($.extend($.sheet.tikiOptions,{
				editable:false'
			. $calcOff .
			'}));
			});'
		);

	}

	$ret = '<div id="tiki_sheet' . $sheet->instance . '" class="tiki_sheet' . $class . '" style="overflow:hidden;' . $style . '">' . $ret . '</div>';

	if ( $editable && ($objectperms->edit_sheet  || $objectperms->admin_sheet || $tiki_p_admin == 'y')) {
		$smarty->loadPlugin('smarty_function_button');

		//If you've given the sheet a url, you can't edit it, disable if not possible
		if (!isset($url)) {
			$button_params = array('_text' => tra("Edit Sheet"), '_script' => "tiki-view_sheets.php?sheetId=$id&parse=edit$urlHeight&page=$page");
		}

		$ret .= smarty_function_button($button_params, $smarty);
	}
	return '~np~' . $ret . '~/np~';
}
