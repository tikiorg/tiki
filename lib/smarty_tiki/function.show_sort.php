<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_show_sort($params, &$smarty) {
	global $url_path;
  if (isset($params['sort']) and isset($params['var']) and isset($_REQUEST[$params['sort']])) {
	  $prop = substr($_REQUEST[$params['sort']],0,strrpos($_REQUEST[$params['sort']],'_'));
		$order = substr($_REQUEST[$params['sort']],strrpos($_REQUEST[$params['sort']],'_')+1);
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
