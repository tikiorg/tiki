<?php

// Displays a snippet of code
// Parameters: bgcolor (optional background color)
// Example:
// {CODE()}
// print("foo");
// {CODE}

function wikiplugin_code($data,$params) {
  global $tikilib;
  extract($params);
  $code=htmlspecialchars(trim($data));
  if(!isset($bgcolor)) {$bgcolor='#EEEEEE';}
  //If you want line numbering use something like this:
  //$lines = explode("\n",$code);
  //print_r($lines);
  $data = "<div style='background-color:$bgcolor;'>".$code.'</div>';
  return $data;
}
?>