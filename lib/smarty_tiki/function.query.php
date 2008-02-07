<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_query($params, &$smarty) {
  global $auto_query_args;

  $query = array_merge($_POST, $_GET);
  if ( is_array($params) ) {
    foreach( $params as $param_name => $param_value ) {
  
      // Arguments starting with an underscore are special and must not be included in URL
      if ( $param_name[0] == '_' ) continue;
  
      $list = explode(",",$param_value);
      if ( isset($query[$param_name]) and in_array($query[$param_name],$list) ) {
        $query[$param_name] = $list[(array_search($query[$param_name],$list)+1)%sizeof($list)];
        if ( $query[$param_name] === NULL or $query[$param_name] == 'NULL' ) {
          unset($query[$param_name]);
        }
      } else {
        if ( $list[0] !== NULL and $list[0] != 'NULL' ) {
          $query[$param_name] = $list[0];
        } else {
          unset($query[$param_name]);
        }
      }
    }
  }

  if ( is_array($query) ) {

    // Only keep params explicitely specified when calling this function or specified in the $auto_query_args global var
    // This is to avoid including unwanted params (like actions : remove, save...)
    if ( is_array($auto_query_args) ) {
      foreach ( $query as $k => $v ) {
        if ( ! in_array($k, $auto_query_args) && ! ( is_array($params) && array_key_exists($k, $params) ) ) {
          unset($query[$k]);
        }
      }
    }

    $ret = '';
    $sep = '&amp;';
    if ( function_exists('http_build_query') ) {
      $ret = http_build_query($query, '', $sep);
    } else {
      foreach ( $query as $k => $v ) {
        if ( $ret != '' ) $ret .= $sep;
	if ( is_array($v) ) {
	  foreach ( $v as $vk => $vv ) {
	    $ret .= urlencode($k.'['.$vk.']').'='.urlencode($vv);
	  }
	} else {
          $ret .= urlencode($k).'='.urlencode($v);
	}
      }
    }
  }

  if ( is_array($params) && isset($params['_type']) ) {
    global $base_host;
    switch ( $params['_type'] ) {
      case 'absolute_uri':
        $ret = $base_host.$_SERVER['PHP_SELF'].'?'.$ret;
        break;
      case 'absolute_path':
        $ret = $_SERVER['PHP_SELF'].'?'.$ret;
        break;
      case 'relative':
	$ret = basename($_SERVER['PHP_SELF']).'?'.$ret;
        break;
      case 'arguments': /* default */
    }
  }

  return $ret;
}
?>
