<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Param: 'id' or 'label'
function smarty_function_content($params, &$smarty) {
  global $tikilib;

  if ( isset($params['id']) ) {
    $data = $tikilib->get_actual_content($params['id']);
  } elseif ( isset($params['label']) ) {
    $data = $tikilib->get_actual_content_by_label($params['label']);
  } else {
    $smarty->trigger_error("assign: missing 'id' or 'label' parameter");
    return false;
  }

  return $data;
}

?>
