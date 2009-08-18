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
 * Name:     kaltura_date_format
 * Purpose:  Changes date-time stamp returned from kaltura server to timestamp
 * Input:    string: input date string
 *           
 * -------------------------------------------------------------
 */
function smarty_modifier_kaltura_date_format($string) {
		
	return date('d M Y h:i A',strtotime($string));
}
