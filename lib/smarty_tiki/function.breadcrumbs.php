<?php
// $Header Exp: $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_breadcrumbs($params, &$smarty)
{
    extract($params);

    if (empty($crumbs)) {
        $smarty->trigger_error("assign: missing 'crumbs' parameter");
        return;
    }
    if (empty($loc)) {
        $smarty->trigger_error("assign: missing 'loc' parameter");
        return;
    }
    switch ($type) {
        case 'fulltrail':
            print(breadcrumb_buildHeadTitle($crumbs));
            break;
        case 'pagetitle':
            print(breadcrumb_getTitle($crumbs, $loc));
            break;
        case 'desc':
            print(breadcrumb_getDescription($crumbs, $loc));
            break;
        case 'trail':
        default:
            print(breadcrumb_buildTrail($crumbs,$loc));
            break;
    }
}
?>
