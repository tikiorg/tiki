<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_memusage($params, &$smarty)
{
    if (function_exists('memory_get_usage')) {
      $memusage=memory_get_usage();
    } else {
      $memusage=0;
    }
    
    if ($memusage>0) {
      $memunit="B";
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="kB";
      }
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="MB";
      }
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="GB";
      }
      print(number_format($memusage,2).$memunit);
    } else {
      print (tra("Unknown"));
    }
}



?>
