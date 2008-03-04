<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_icon: Display a Tikiwiki icon, using theme icons if they exists
 *
 * params will be used as params for the HTML tag (e.g. border, class, ...), except special params starting with '_' :
 *  - _id: short name (i.e. 'page_edit') or relative file path (i.e. 'pics/icons/page_edit.png'). [required]
 *  - _type: type of URL to use (e.g. 'absolute_uri', 'absolute_path'). Defaults to a relative URL.
 *  - _tag: type of HTML tag to use (e.g. 'img', 'input_image'). Defaults to 'img' tag.
 *  - _notag: if set to 'y', will only return the URL (which also handles theme icons).
 *  - _menu_text: if set to 'y', will use the 'title' argument as text after the icon and place the whole content between div tags with a 'icon_menu' class (not compatible with '_notag' param set to 'y').
 *  - _menu_icon: if set to 'n', will not show icon image when _menu_text is 'y'.
 *  - _confirm: text to use in a popup requesting the user to confirm it's action (yet only available with javascript)
 */
function smarty_function_icon($params, &$smarty) {
  if ( ! is_array($params) || ! isset($params['_id']) ) return;

  $basedirs = array('pics/icons', 'images', 'img/icons', 'pics/icons/mime');
  $icons_extension = '.png';
  $tag = 'img';
  $notag = false;
  $default_class = 'icon';
  $default_width = 16;
  $default_height = 16;
  $menu_text = false;
  $menu_icon = true;
  $confirm = '';
  $html = '';

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

  global $smarty, $prefs, $style_base, $tikidomain, $tikipath, $url_path, $base_url;

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
        case '_menu_text':
          $menu_text = ($v == 'y');
          $menu_icon = ( ! isset($params['_menu_icon']) || $params['_menu_icon'] == 'y' );
          break;
        case '_tag':
          $tag = $v;
          break;
        case '_confirm':
          if ( $prefs['javascript_enabled'] == 'y' ) {
            $params['onclick'] = "return confirm('".str_replace("'", "\'", $v)."');";
          }
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
  if ( $tag == 'img' ) {
    if ( ! isset($params['width']) ) $params['width'] = $default_width;
    if ( ! isset($params['height']) ) $params['height'] = $default_height;
  }

  if ( $notag ) {
    $html = $params['path_prefix'].$params['file'];
  } else {

    // use 'alt' as 'title' if not set
    if ( ! isset($params['title']) ) $params['title'] = $params['alt'];
    // use default class if not set
    if ( ! isset($params['class']) ) $params['class'] = $default_class;

    // remove empty arguments
    foreach ( $params as $k => $v ) {
      if ( $v == '' ) unset($params[$k]);
    }

    // No need to add a title on a menu icon since there will be the same text just after the icon
    if ( $menu_text ) {
      $menu_text_val = $params['title'];
      unset($params['title']);
    }

    if ( $menu_icon ) {
      if ( $tag != 'img' ) {
        $params['src'] = $params['file'];
        unset($params['file']);
        foreach ( $params as $k => $v ) {
          $html .= ' '.htmlspecialchars($k, ENT_QUOTES, 'UTF-8').'="'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'"';
        }
      }
      switch ( $tag ) {
        case 'input_image': $html = '<input type="image"'.$html.' />'; break;
        case 'img': default: $html = smarty_function_html_image($params, $smarty);
      }
      if ( $tag != 'img' ) {
        // Add a span tag to be able to apply a CSS style on hover for the icon
        $html = "<span>$html</span>";
      }
    }
    if ( $menu_text ) $html = '<div class="iconmenu">'.$html.'<span class="iconmenutext"> '.$menu_text_val.'</span></div>';

  }

  return $html;
}

?>
