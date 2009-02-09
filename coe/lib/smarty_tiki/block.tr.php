<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.translate.php
 * Type:     block
 * Name:     translate
 * Purpose:  translate a block of text
 * -------------------------------------------------------------
 */
 
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

include_once('lib/init/tra.php');

function smarty_block_tr($params, $content, &$smarty) {

	if ($content == '')
		return;
	if (empty($params['lang'])) {
		$lang = '';
	} else {
		$lang = $params['lang'];
	}

	$args = array();
	foreach( $params as $key => $value ) {
		if( is_int( $key ) )
			$args[$key] = $value;
	}

	if (empty($params['interactive']) || $params['interactive'] == 'y')
		return tra($content,$lang, false, $args);
	else
		return tra($content,$lang, true);
}
?>
