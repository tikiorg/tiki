<?php

// Definition lists
// Usage:
// {DL()}
// term1:def1
// term2:def2
// {DL}
//

function wikiplugin_dl($data,$params) {
  global $tikilib;
  global $replacement;
  extract($params);
  $result='<dl>';
  $lines = split("\n",$data);
  foreach($lines as $line) {
    $parts = explode(":",$line);
    if(isset($parts[0])&&isset($parts[1])) {
      $result.='<dt>'.$parts[0].'</dt><dd>'.$parts[1].'</dd>';
    }
  }
  $result.='</dl>';
  return $result;
}
?>