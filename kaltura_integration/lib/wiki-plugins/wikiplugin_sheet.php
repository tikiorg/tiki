<?php
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
	return tra("TikiSheet").":<br />~np~{SHEET(id=>)}".tra("Sheet Heading")."{SHEET}~/np~";
}

function wikiplugin_sheet_info() {
	return array(
		'name' => tra('Sheet'),
		'documentation' => 'PluginSheet',
		'description' => tra('Displays the content of a spreadsheet in the page.'),
		'prefs' => array( 'wikiplugin_sheet', 'feature_sheet' ),
		'body' => tra('Sheet Heading'),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Sheet ID'),
				'description' => tra('Internal ID of the TikiSheet.'),
			),
		),
	);
}

function wikiplugin_sheet($data, $params) {
	global $dbTiki, $tiki_p_edit_sheet, $tiki_p_edit, $tiki_p_admin_sheet, $tiki_p_admin, $prefs, $user, $sheetlib, $page, $tikilib;
	extract ($params,EXTR_SKIP);

	if( !class_exists( 'TikiSheet' ) )
		require "lib/sheet/grid.php";

	static $index = 0;
	++$index;

	if (!isset($id)) {
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
				$label = tra('Create new sheet');
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

	// Build required objects
	$sheet = new TikiSheet;
	$db = new TikiSheetDatabaseHandler( $id );
	$out = new TikiSheetOutputHandler( $data );

	// Fetch sheet from database
	$sheet->import( $db );
	
	// Grab sheet output
	ob_start();
	$sheet->export( $out );
	$ret = ob_get_contents();
	ob_end_clean();

	if( $tiki_p_edit_sheet == 'y' || $tiki_p_admin_sheet == 'y' || $tiki_p_admin == 'y')
		$ret .= "<a href='tiki-view_sheets.php?sheetId=$id&readdate=" . time() . "&mode=edit' class='linkbut'>" . tra("Edit Sheet") . "</a>";
	
	return $ret;
}
