<?php


function smarty_function_sameurl($params, &$smarty)
{
    $data = $_SERVER['SCRIPT_NAME'];
    $first=true;
    $sets=Array();
    foreach($params as $name=>$val) {
    	if(isset($_REQUEST[$name])) {
    	  $_REQUEST[$name]=$val;
    	} else {
      		if($first) {
        		$first = false;
        		$sep='?';
      		} else {
        		$sep='&amp;';
      		}	
      		if(!is_array($name)&&!is_array($val)) {
 	        if(!in_array($name,$sets)) {
        		$data.=$sep.urlencode($name).'='.urlencode($val);
        		$sets[]=$name;
      		}
      		}

    	}
    }
    
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
      if(!is_array($name)&&!is_array($val)) {
      if(!in_array($name,$sets)) {
        $data.=$sep.urlencode($name).'='.urlencode($val);
        $sets[]=$name;
      }
      }
    }
    print($data);
}

/* vim: set expandtab: */

?>
