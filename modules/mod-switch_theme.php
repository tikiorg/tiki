<?php
if(isset($_COOKIE['tiki-theme']) && !($feature_userPreferences == 'y' && $user && $change_theme == 'y')){
	$style = $_COOKIE['tiki-theme'];
}

//Create a list of styles
$styleslist = Array();
$h = opendir("styles/");
while ($file = readdir($h)) {
  if (substr($file,0,1) != '.' and substr($file,-4,4) == ".css" and $file != 'blank.css') {
    $styleslist[] = $file;
  }
}
closedir($h);
sort($styleslist);
$smarty->assign('styleslist',$styleslist);
if(isset($style)){
	$smarty->assign('style', $style);
}
?>
