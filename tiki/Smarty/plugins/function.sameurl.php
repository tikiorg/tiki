<?php


function smarty_function_sameurl($params, &$smarty)
{
    $data = $_SERVER['SCRIPT_NAME'];
    $first=true;
    $sets=Array();
    extract($params);
    foreach($_REQUEST as $name=>$val) {
      if($first) {
        $first = false;
        $sep='?';
      } else {
        $sep='&amp;';
      }
      if(isset($$name)) {
        $val = $$name;
      }

      if(!in_array($name,$sets)) {
        $data.=$sep.urlencode($name).'='.urlencode($val);
        $sets[]=$name;
      }
    }
    print($data);
}

/* vim: set expandtab: */

?>
