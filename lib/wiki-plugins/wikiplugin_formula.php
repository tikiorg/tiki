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
function wikiplugin_formula_help() {
	return tra("Formula").":<br />~np~{FORMULA(width=>500, height=>400, paper=>letter, orientation=>landscape, steps=>150, min=0, max=100, y0=>, y1=>,...)}".tra("Title")."{FORMULA}~/np~";
}
function wikiplugin_formula($data, $params) {
	global $dbTiki, $tikilib, $tiki_p_edit_sheet, $tiki_p_admin_sheet, $tiki_p_admin, $prefs;

	if ($prefs['feature_sheet'] != 'y') {
		return (tra("This feature is disabled").": feature_sheet");
	}
	
	extract ($params,EXTR_SKIP);

	if (!isset($width)) $width = 500;
	if (!isset($height)) $height = 400;
	if (!isset($paper)) $paper = 'letter';
	if (!isset($orientation)) $orientation = 'landscape';
	if (!isset($steps)) $steps = 150;
	if (!isset($min)) $min = 50;
	if (!isset($max)) $max = 50;

	$found = false;
	$qs = "w=$width&h=$height&p=$paper&o=$orientation&s=$steps&min=$min&max=$max&title=" . urlencode( $data );
	for( $i = 0; isset( ${'y'.$i} ); $i++ )
	{
		$form = ${'y'.$i};

		if( !empty( $form ) )
		{
			$found = true;
			$qs .= "&f[]=$form";
		}
	}

	if( !$found )
		return tra("No formula specified.");

	$text = tra("Impossible to render the graphic.");

	if( function_exists( 'imagepng' ) )
		$text = "<img src='tiki-graph_formula.php?t=png&$qs'/>";
	elseif( function_exists( 'pdf_new' ) )
		$text = tra("View graphic");

	if( function_exists( 'pdf_new' ) )
		$text = "<a href='tiki-graph_formula.php?t=pdf&$qs'>$text</a>";

	return $text;
}

?>
