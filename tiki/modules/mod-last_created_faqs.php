<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $faqlib;
if (!is_object($faqlib)) {
	include_once('lib/faqs/faqlib.php');
}
$ranking = $faqlib->list_faqs(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastCreatedFaqs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
