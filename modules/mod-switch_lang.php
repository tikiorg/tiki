<?php
// tiki-setup has already set the $language variable
//Create a list of languages
$languages = array();
$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);
?>
