<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/js/wiki-plugins/wikiplugin_js.php,v 1.2 2007-03-27 13:51:12 sylvieg Exp $
 * file = external javascript file
 * data is the javascript code
 */
function wikiplugin_js_help() {
	return tra("Insert a javascript file or/and some javascript code.")."<br />~np~{JS(file=file.js)}".tra("javascript code")."{JS}~/np~";
}

function wikiplugin_js($data, $params) {
	extract($params, EXTR_SKIP);
	$ret = '';
	if ($data) {
		$ret .= "~np~<script type=\"text/javascript\">".$data."</script>~/np~"; 
	}
	// the order data then file is important for google adsense
	if (isset($file)) {
		$ret .=  "~np~<script type=\"text/javascript\" src=\"$file\"></script> ~/np~";
	}
	return $ret;
}
?>
