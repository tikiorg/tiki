<?php

require_once("tiki-setup.php");
require_once("lib/xajax/xajax.inc.php");


#---------------------------------Functions used by xajaxlib - it can be an include

function tra_ajax($response) {
 $objResponse = new xajaxResponse();
 $objResponse->addAssign("result", "innerHTML", tra($response['str'],$response['lang']));
 return $objResponse;
}

function get_template($tpl) {
  
  $sourceCode = file_get_contents("templates/".$tpl);
  $sourceCode = "<pre>" . htmlspecialchars($sourceCode) . "</pre>";
  $objResponse = new xajaxResponse();
  $objResponse->addAssign("template-source", "innerHTML", $sourceCode);
  
  return $objResponse;
}

#-------------------------------------------------------------------------------------

$xajax = new xajax();
# registering the functions - xajax will generate the js code.
$xajax->registerFunction("tra_ajax");
$xajax->registerFunction("get_template");
$xajax->processRequests();

#assigning the js code to: xajax_js -> this var will be printed in the template file - {$xajax_js}
$smarty->assign("xajax_js",$xajax->getJavascript());

$smarty->assign('mid','tiki-xajax_example.tpl');
$smarty->display('tiki.tpl');
?>
