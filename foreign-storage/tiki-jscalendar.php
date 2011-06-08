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

if ( $prefs['feature_jscalendar'] == 'y' && $prefs['javascript_enabled'] == 'y' ) {
	$jslang['en'] = "en";
	$jslang['fr'] = "fr-utf8";
	$jslang['pt-br'] = "br-utf8";
	$jslang['es'] = "es-utf8";
	if (isset($prefs['language']) and isset($jslang[$prefs['language']])) {
		$smarty->assign('jscalendar_langfile', $jslang[$prefs['language']]);
	} else {
		$smarty->assign('jscalendar_langfile', array_shift($jslang));
	}
}
