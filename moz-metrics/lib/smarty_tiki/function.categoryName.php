<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_categoryName($params, &$smarty) {
    if( ! isset( $params['id'] ) ) {
        $smarty->trigger_error("categoryName: missing 'id' parameter");
        return;
    }

	global $categlib; require_once 'lib/categories/categlib.php';
	return $categlib->get_category_name( $params['id'] );
}
