<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// tiki-setup has already set the $language variable
//Create a list of languages
$languages = array();
$languages = $tikilib->list_languages(false, 'y');
$smarty->assign_by_ref('languages', $languages);
?>
