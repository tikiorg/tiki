<?php
  // $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_prefilter_log_tpl($source, &$smarty) {
	global $prefs;
	if ($prefs['log_tpl'] != 'y')
		return $source;
	return '<!-- TPL: '.$smarty->_current_file.' -->'.$source.'<!-- /TPL: '.$smarty->_current_file.' -->';
}
?>
