<?php

// Centers the plugin content in the wiki page
// Usage
// {CENTER()}
//  data
// {CENTER}

function wikiplugin_center_help() {
  return "Centers the plugin content in the wiki page";
}

function wikiplugin_center($data,$params) {
  global $tikilib;
  extract($params);
  $data = '<div align="center">'.trim($data).'</div>';
  return $data;
}

?>
