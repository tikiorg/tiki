<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$content = $tikilib->get_file($module_params["fileId"]);

    $parser = xml_parser_create();
    xml_parse_into_struct($parser,$content["data"],$d_ar,$i_ar);
    

    foreach($d_ar as $element)
    {
    	// Outline attributes signal a feed
      if($element["tag"] == 'OUTLINE' && ( $element["type"] == 'complete' || $element["type"] == 'open' || $element["type"] == 'close' ) )
      {
        $feeds[] = $element;
      } 
    }

$smarty->assign('feeds', $feeds);
$smarty->assign('fileId', $module_params['fileId']);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
