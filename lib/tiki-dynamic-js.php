<?php
session_start();
header( "Content-Type: text/javascript" );

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$list = array();
if( is_array( $_SESSION['tiki_cookie_jar'] ) )
	foreach( $_SESSION['tiki_cookie_jar'] as $name=>$value )
		$list[] = $name . ": '" . addslashes($value) . "'";
?>
var tiki_cookie_jar = {
	<?=implode( ",\n\t", $list )?>
};
