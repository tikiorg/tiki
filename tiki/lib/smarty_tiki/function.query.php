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
      if ( isset($_REQUEST[$param_name]) and in_array($_REQUEST[$param_name],$list) ) {
        $query[$param_name] = $list[(array_search($_REQUEST[$param_name],$list)+1)%sizeof($list)];
        if ( $query[$param_name] === NULL or $query[$param_name] == 'NULL' ) {
          unset($query[$param_name]);
        }
      } elseif ( isset($query[$param_name]) and in_array($query[$param_name],$list) ) {
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
    if ( isset($params['_type']) && $params['_type'] == 'form_input' ) {
      foreach ( $query as $k => $v ) {
        $ret .= '<input type="hidden"'
             .' name="'.htmlspecialchars($k, ENT_QUOTES, 'UTF-8').'"'
             .' value="'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'" />'."\n";
      }
    } else {
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

  }

  if ( is_array($params) && isset($params['_type']) ) {
    global $base_host;

    // If specified, use _script argument to determine the php script to link to
    // ... else, use PHP_SELF server var
    if ( isset($params['_script']) && $params['_script'] != '' ) {
      $php_self = $params['_script'];

      // If _script does not already specifies the directory and if there is one in PHP_SELF server var, use it
      if ( strpos($php_self, '/') === false && $_SERVER['PHP_SELF'][0] == '/' ) {
        $php_self = dirname($_SERVER['PHP_SELF']).'/'.$php_self;
      }

    } else {
      $php_self = $_SERVER['PHP_SELF'];
    }

    switch ( $params['_type'] ) {
      case 'absolute_uri':
        $ret = $base_host.$php_self.'?'.$ret;
        break;
      case 'absolute_path':
        $ret = $php_self.'?'.$ret;
        break;
      case 'relative':
	$ret = basename($php_self).'?'.$ret;
        break;
      case 'form_input': case 'arguments': /* default */
    }
  }

  return $ret;
}
?>
