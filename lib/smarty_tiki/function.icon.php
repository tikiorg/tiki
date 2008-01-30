<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_icon: Display a Tikiwiki icon, using theme icons if they exists
 *
 * params will be used as params for the IMG tag (e.g. border, class, ...), except special params starting with '_' :
 *  - _id: short name (i.e. 'page_edit') or relative file path (i.e. 'pics/icons/page_edit.png'). [required]
 *  - _type: type of URL to use (e.g. 'absolute_uri', 'absolute_path'). Defaults to a relative URL.
 *  - _notag: if set to 'y', will only return the URL (which also handles theme icons).
 */
function smarty_function_icon($params, &$smarty) {
  if ( ! is_array($params) || ! isset($params['_id']) ) return;

  $basedirs = array('pics/icons', 'images', 'img/icons');
  $icons_extension = '.png';
  $notag = false;
  $default_class = 'icon';

  // Handle _ids that contains the real filename and path
  if ( strpos($params['_id'], '/') !== false || strpos($params['_id'], '.') !== false ) {
    if ( ($icons_basedir = dirname($params['_id'])) == '' || ! in_array($icons_basedir, $basedirs) ) $icons_basedir = $basedirs[0];
    $icons_basedir .= '/';
    if ( ($pos = strrpos($params['_id'], '.')) !== false ) $icons_extension = substr($params['_id'], $pos);
    $params['_id'] = ereg_replace('(^'.$icons_basedir.'|'.$icons_extension.'$)', '', $params['_id']);
  } else {
    $icons_basedir = $basedirs[0].'/';
  }

  if ( ! eregi('^[a-z0-9_]+$', $params['_id']) ) return;

  global $smarty, $style_base, $tikidomain, $tikipath, $url_path, $base_url;

  // Include smarty functions used below
  require_once $smarty->_get_plugin_filepath('function', 'html_image');

  // auto-detect 'alt' param if not set
  if ( ! isset($params['alt']) ) {
    $alt_pos = ( ($alt_pos = strrpos($params['_id'], '_')) === false ) ? 0 : $alt_pos + 1;
    $params['alt'] = tra( ucfirst( substr($params['_id'], $alt_pos) ) );
  }

  // handle special params and clean unrecognized params
  foreach ( $params as $k => $v ) {
    if ( $k[0] == '_' ) {
      switch ( $k ) {
        case '_id':
          $v = $icons_basedir.$v.$icons_extension;
          if ( isset($style_base) ) {
            if ( $tikidomain and file_exists($tikipath."/styles/$tikidomain/$style_base/$v") ) {
              $params['file'] = "styles/$tikidomain/$style_base/$v";
            } elseif ( $tikidomain and file_exists($tikipath."/styles/$tikidomain/$v") ) {
              $params['file'] = "$tikidomain/$v";
            } elseif ( file_exists($tikipath."/styles/$style_base/$v") ) {
              $params['file'] = "styles/$style_base/$v";
            } else {
              $params['file'] = $v;
            }
          }
          break;
        case '_type':
          switch ( $v ) {
            case 'absolute_uri':
              $params['path_prefix'] = $base_url;
              break;
            case 'absolute_path':
              $params['path_prefix'] = $url_path;
              break;
          }
          break;
        case '_notag':
          $notag = ($v == 'y');
          break;
      }
      unset($params[$k]);
    }
  }

  // default values for some params
  if ( ! isset($params['border']) ) $params['border'] = '0';
  if ( isset($params['path_prefix']) ) {
    $params['basedir'] = $tikipath;
    $params['file'] = '/'.$params['file'];
  }

  if ( $notag ) {
    $html = $params['path_prefix'].$params['file'];
  } else {

    // use 'alt' as 'title' if not set
    if ( ! isset($params['title']) ) $params['title'] = $params['alt'];
    // use default class if not set
    if ( ! isset($params['class']) ) $params['class'] = $default_class;

    $html = smarty_function_html_image($params, $smarty);
  }

  return $html;
}

?>
