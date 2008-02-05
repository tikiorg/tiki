<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_show_sort($params, &$smarty) {
	global $url_path;
	if (isset($_REQUEST[$params['sort']])) {
		$p =  $_REQUEST[$params['sort']];
	} elseif ($s = $smarty->get_template_vars($params['sort'])) {
		$p = $s;
	}
  if (isset($params['sort']) and isset($params['var']) and isset($p)) {
	  $prop = substr($p,0,strrpos($p,'_'));
		$order = substr($p,strrpos($p,'_')+1);
		if (strtolower($prop) == strtolower(trim($params['var']))) {
		  switch($order) {
			  case 'asc':
				  return "<img style='border:none;vertical-align:middle' src='".$url_path."pics/icons/resultset_up.png'/>";
					break;
			  case 'desc':
				  return "<img style='border:none;vertical-align:middle' src='".$url_path."pics/icons/resultset_down.png'/>";
					break;
			}
		}
	}
}
?>
