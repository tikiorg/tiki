<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Param: 'id' or 'label'
function smarty_function_interactivetranslation($params, &$smarty) {
	global $headerlib, $smarty;

	$strings = get_collected_strings();
	if( count( $strings ) == 0 ) {
		return;
	}

	usort( $strings, 'sort_strings_by_length' );

	$strings = json_encode( $strings );
	
	// add wrench icon link
	require_once $smarty->_get_plugin_filepath('block', 'self_link');
	$help .= smarty_block_self_link(
		array( '_icon'=>'wrench',
			   '_script'=>'tiki-edit_languages.php',
			   '_title'=>tra('Click here to go to Edit Languages')),
		'', $smarty);
	
	$jq = <<<JS
	var data = $strings;
JS;

	$headerlib->add_jq_onready($jq);
	$headerlib->add_jq_onready(file_get_contents('lib/language/js/interactive_translation.js'));

	return $smarty->fetch('interactive_translation_box.tpl');
}

function sort_strings_by_length( $a, $b ) {
	$a = strlen( $a[1] );
	$b = strlen( $b[1] );

	if( $a == $b ) {
		return 0;
	} elseif( $a > $b ) {
		return -1;
	} else {
		return 1;
	}
}
