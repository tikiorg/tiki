<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_content($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    global $dcslib;
    include_once('lib/dcs/dcslib.php');
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $dcslib->get_actual_content($id);
    print($data);
}

?>
