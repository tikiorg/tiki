<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_query($params, &$smarty) {
  parse_str(htmlspecialchars_decode($_SERVER['QUERY_STRING']),$query);
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
  echo http_build_query($query,'', '&amp;');
}
?>
