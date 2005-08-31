<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/js/wiki-plugins/wikiplugin_js.php,v 1.1 2005-08-31 14:09:43 sylvieg Exp $
 * file = external javascript file
 * data is the javascript code
 */
function wikiplugin_js_help() {
	return tra("Insert a javascript file or/and some javascript code.")."<br />~np~{JS(file=file.js)}".tra("javascript code")."{JS}~/np~";
}

function wikiplugin_js($data, $params) {
	extract($params, EXTR_SKIP);
	if(isset($file)) {
		$ret =  "~np~<script type=\"text/javascript\" src=\"$file\"></script> ~/np~";
	} else {
		$ret = '';
	}
	if ($data) {
		$ret .= "~np~<script type=\"text/javascript\">".$data."</script>~/np~"; 
	}
	return $ret;
}
?>
