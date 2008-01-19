<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_query($params, &$smarty) {
  $query = array_merge($_POST, $_GET);
  foreach($params as $param_name=>$param_value) {
    $list = explode(",",$param_value);
    if (isset($query[$param_name])) {
      if ($param_value === NULL or $param_value == 'NULL') {
        unset($query[$param_name]);
        continue;
      }
    $query[$param_name] = $list[(array_search($query[$param_name],$list)+1)%sizeof($list)];
		if ($query[$param_name] === NULL or $query[$param_name] == 'NULL') {
		  unset($query[$param_name]);
		}
    } else {
      if ($param_value !== NULL and $param_value != 'NULL' ) {
        $query[$param_name] = $list[0];
      }
    }
  }

  $ret = '';
  $sep = '&amp;';
  if ( function_exists('http_build_query') ) {
    $ret = http_build_query($query, '', $sep);
  } else {
    if ( is_array($query) ) {
      foreach ( $query as $k => $v ) {
        if ( $ret != '' ) $ret .= $sep;
        $ret .= urlencode($k).'='.urlencode($v);
      }
    }
  }
  echo $ret;
}
?>
