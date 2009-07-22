<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     kaltura_media_type
 * Purpose:  Maps media type id to media type name
 * Input:    string: input date string
 *           
 * -------------------------------------------------------------
 */
function smarty_modifier_kaltura_media_type($int) {
	
	$mediaType = array("Any","Video","Image","Text","HTML","Audio","Video Remix","SHOW_XML","","Bubbles","XML","Document");
	return $mediaType[(int)$int];
	
}
