<?php

// Displays a snippet of code
// Parameters: bgcolor (optional background color)
// Example:
// {CODE()}
// print("foo");
// {CODE}


function wikiplugin_code_help() {
  return 'Displays a snippet of code';
}

function wikiplugin_code($data,$params) {
  global $tikilib;
  global $style;

  extract($params);
  $code=htmlspecialchars(trim($data));
  if(!isset($bgcolor)) {$bgcolor='#EEEEEE';}
  //If you want line numbering use something like this:
  //$lines = explode("\n",$code);
  //print_r($lines);
  
  if (strstr($style, "zaufi"))
    $codestyle="class='codelisting'";
  else
    $codestyle="style='border: 1px solid #CCCCCC;margin: 4px;padding-left: 10px;background-color:$bgcolor;'";
  // Wrap listing into div
  $data = "<div $codestyle><pre>".$code."</pre></div>";

  return $data;
}
?>