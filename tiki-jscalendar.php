<?php
if (isset($feature_jscalendar) and $feature_jscalendar == 'y') {
	$jslang['en'] = "en";
	$jslang['fr'] = "fr";
  $smarty->assign('uses_jscalendar', 'y'); 
  $smarty->assign('feature_jscalendar', 'y'); 
	if (isset($language) and isset($jslang["$language"])) {
		$smarty->assign('jscalendar_langfile', $jslang["$language"]);
	} else {
		$smarty->assign('jscalendar_langfile', array_shift($jslang));
	}
}
?>
