<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}	

if (isset($prefs['feature_jscalendar']) and $prefs['feature_jscalendar'] == 'y') {
	$jslang['en'] = "en";
	$jslang['fr'] = "fr-utf8";
	$jslang['pt-br'] = "br-utf8";
	$jslang['es'] = "es-utf8";
  $smarty->assign('uses_jscalendar', 'y'); 
  $smarty->assign('feature_jscalendar', 'y'); 
	if (isset($prefs['language']) and isset($jslang[$prefs['language']])) {
		$smarty->assign('jscalendar_langfile', $jslang[$prefs['language']]);
	} else {
		$smarty->assign('jscalendar_langfile', array_shift($jslang));
	}
}
?>
