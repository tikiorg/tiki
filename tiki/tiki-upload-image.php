<?php
// Initialization
require_once('tiki-setup.php');

// Now check permissions to access this page
/*
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages like this page"));
  $smarty->display('error.tpl');
  die;  
}
*/

// Process an upload here

// Get the list of galleries to display the select box in the template


// Display the template
$smarty->assign('mid','tiki-upload-image.tpl');
$smarty->display('tiki.tpl');
?>
