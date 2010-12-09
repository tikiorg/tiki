<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_query($params, &$smarty) {
	global $auto_query_args;
	static $request = NULL;

	if ( isset($params['_noauto']) && $params['_noauto'] == 'y' ) {
		$query = array();
		foreach( $params as $param_name => $param_value ) {
			if ( $param_name[0] == '_' || $param_value == 'NULL' || $param_value == NULL ) continue;
			$query[$param_name] = $param_value;
		}
		// Even if _noauto is set, 'filegals_manager' is a special param that has to be kept all the time
		if ( ! isset($params['filegals_manager']) && isset($_REQUEST['filegals_manager']) ) {
			$query['filegals_manager'] = $_REQUEST['filegals_manager'];
		}
	} else {
		// Not using _REQUEST here, because it is sometimes directly modified in scripts
		if ( $request === NULL ) {
			$request = array_merge($_GET, $_POST);

			// Remove Xajax special arguments
			foreach ( array('xjxargs', 'xjxr', 'xjx', 'xjxfun', 'xjxr') as $k ) {
				unset($request[$k]);
			}
		}
		$query = $request;

		if ( is_array($params) ) {
			foreach( $params as $param_name => $param_value ) {
				// Arguments starting with an underscore are special and must not be included in URL
				if ( $param_name[0] == '_' ) continue;
	
				$list = explode(",",$param_value);
				if ( isset($_REQUEST[$param_name]) and in_array($_REQUEST[$param_name],$list) ) {
					$query[$param_name] = $list[(array_search($_REQUEST[$param_name],$list)+1)%count($list)];
					if ( $query[$param_name] === NULL or $query[$param_name] == 'NULL' ) {
						unset($query[$param_name]);
					}
				} elseif ( isset($query[$param_name]) and in_array($query[$param_name],$list) ) {
					$query[$param_name] = $list[(array_search($query[$param_name],$list)+1)%count($list)];
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
	}

  if ( is_array($query) ) {

    // Only keep params explicitely specified when calling this function or specified in the $auto_query_args global var
    // This is to avoid including unwanted params (like actions : remove, save...)
    if ( ( ! isset($params['_keepall']) || $params['_keepall'] != 'y' ) && is_array($auto_query_args) ) {
      foreach ( $query as $k => $v ) {
        if ( ! in_array($k, $auto_query_args) && ! ( is_array($params) && array_key_exists($k, $params) ) ) {
          unset($query[$k]);
        }
      }
    }

    $ret = '';
    if ( isset($params['_type']) && $params['_type'] == 'form_input' ) {
      foreach ( $query as $k => $v ) {
        $rtag = '<input type="hidden"';
        $rname = htmlspecialchars($k, ENT_QUOTES, 'UTF-8');
        if ( is_array($v) ) {
          foreach ( $v as $vk => $vv ) {
            $vrname = $rname.'['.htmlspecialchars($vk, ENT_QUOTES, 'UTF-8').']';
            $ret .= $rtag.' name="'.$vrname.'" value="'.htmlspecialchars($vv, ENT_QUOTES, 'UTF-8').'" />'."\n";
          }
        } else {
          $ret .= $rtag.' name="'.$rname.'" value="'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'" />'."\n";
        }
      }
    } else {
      if ( ! isset($params['_urlencode']) ) {
        $params['_urlencode'] = 'y';
      }
      $sep = $params['_urlencode'] == 'n' ? '&' : '&amp;';
      $ret = http_build_query($query, '', $sep);
    }

  }

  if ( is_array($params) && isset($params['_type']) ) {
    global $base_host;

		// Check for anchor used as script
	if ( !empty($params['_script'][0]) && $params['_script'][0] == '#' ) {
			if ( empty($params['_anchor']) ) {
				$params['_anchor'] = substr($params['_script'],1);
			}
			unset($params['_script']);
		}

    // If specified, use _script argument to determine the php script to link to
    // ... else, use PHP_SELF server var
    if ( isset($params['_script']) && $params['_script'] != '' ) {
      $php_self = $params['_script'];

      // If _script does not already specifies the directory and if there is one in PHP_SELF server var, use it
      if ( $php_self != 'javascript:void(0)' && strpos($php_self, '/') === false && $_SERVER['PHP_SELF'][0] == '/' ) {
        $php_self = str_replace('\\','/',dirname($_SERVER['PHP_SELF'])).'/'.$php_self;
      }

    } elseif ( empty($params['_anchor']) || ! empty($ret) ) {

      // Use current script explicitely, except if there is only an anchor (i.e. no script and no URL argument) which is enough
      // This also implies that if no anchor, every current URL params will be loosed
      //
      $php_self = $_SERVER['PHP_SELF'];

    } else {

      // If we just have an anchor, return only this anchor, usual types other than 'anchor' are irrelevant
      $params['_type'] = 'anchor';

    }

    switch ( $params['_type'] ) {
      case 'absolute_uri':
        $ret = $base_host.$php_self.( $ret == '' ? '' : '?'.$ret );
        break;
      case 'absolute_path':
        $ret = $php_self.( $ret == '' ? '' : '?'.$ret );
        break;
      case 'relative':
	$ret = basename($php_self).( $ret == '' ? '' : '?'.$ret );
        break;
      case 'form_input': case 'arguments': case 'anchor': /* default */
    }
  }

  if ( isset($params['_anchor']) )
    $ret .= '#' . $params['_anchor'];

  return $ret;
}
