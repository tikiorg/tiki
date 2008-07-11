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
	// do not translate english for performance reasons
	if ($prefs['language'] == 'en' && $lg == '' && !isset($_SESSION['interactive_translation_mode'])) return ($content);

	if (empty($content) )
		return;
	if (empty($params['lang'])) {
		$lang = '';
	} else {
		$lang = $params['lang'];
	}
	if (empty($params['interactive']) || $params['interactive'] == 'y')
		return tra($content,$lang);
	else
		return tra($content,$lang, true);
}
?>
