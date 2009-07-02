<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_cookie($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone
   
    $data = $tikilib->pick_cookie();
    print($data);
}



?>
