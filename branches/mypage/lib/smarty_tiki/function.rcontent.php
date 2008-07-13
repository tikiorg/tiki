<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_rcontent($params, &$smarty) {
    global $dcslib; include_once('lib/dcs/dcslib.php');
    return $dcslib->get_random_content($params['id']);
}



?>
