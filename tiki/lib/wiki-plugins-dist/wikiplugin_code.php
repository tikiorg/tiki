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
  $code=trim(highlight_string(trim($data),true));
  if(!isset($bgcolor)) {$bgcolor='#EEEEEE';}
  $data = "<div align='center'><div style='width:70%;margin-top:4px; text-align:left; border: 1px solid black; margin-bottom:4px; background-color:".$bgcolor.";'>".$code.'</div></div>';
  return $data;
}


?>

