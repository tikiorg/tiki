<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_block_self_link : add a link (with A tag) to the current page on a text (passed through $content argument).
 *
 *   The generated link uses other smarty functions like query and show_sort to handle AJAX, sorting fields, and sorting icons.
 *   This block is very useful to handle table columns sorting links.
 *
 * params are the same as smarty 'query' function + some special params starting with an underscore:
 *   _sort_field : name of the field used for sorting,
 *   _sort_arg : name of the URL argument that contains the field to use for sorting. Defaults to 'sort',
 *   _ajax : if set to 'n', will force disabling AJAX even if the ajax feature is enabled,
 *   _tag : if set to 'n', will only return an URL, not the full A tag + text (AJAX and sorting features are not available in this case),
 *   _class : CSS class to use for the A tag
 *   _template : (see smarty query function 'template' param)
 *   _htmlelement : (see smarty query function 'htmlelement' param)
 *   _icon : name of the icon to use (e.g. 'page_edit', 'cross', ...)
 *   _icon_class : CSS class to use for the icon's IMG tag
 *   _menu_text : (see smarty icon function)
 *   _menu_icon : (see smarty icon function)
 *   _title : tooltip to display when the mouse is over the link. Use $content when _icon is used.
 *   _alt : alt attribute for the icon's IMG tag (use _title if _alt is not specified).
 *   _script : specify another script than the current one (this disable AJAX for this link when the current script is different).
 */
function smarty_block_self_link($params, $content, &$smarty, $repeat = false) {
    global $prefs;
    $default_type = 'absolute_path';
    $default_icon_type = 'relative';

    if ( $repeat ) return;
    require_once $smarty->_get_plugin_filepath('function', 'query');

    if ( is_array($params) ) {
      if ( ! isset($content) ) $content = '';
      if ( ! isset($params['_ajax']) ) $params['_ajax'] = 'y';
      if ( ! isset($params['_tag']) ) $params['_tag'] = 'y';
      if ( ! isset($params['_sort_arg']) ) $params['_sort_arg'] = 'sort';
      if ( ! isset($params['_sort_field']) ) {
        $params['_sort_field'] = '';
      } elseif ( $params['_sort_arg'] != '' and ! isset($params[$params['_sort_arg']]) ) {
	        $params[$params['_sort_arg']] = $params['_sort_field'].'_asc,'.$params['_sort_field'].'_desc';
      }
      // Complete _script path if needed
      if ( isset($params['_script']) ) {
        if ( $params['_script'] != '' && $_SERVER['PHP_SELF'][0] == '/' && strpos($params['_script'], '/') === false ) {
          $params['_script'] = ( $_SERVER['PHP_SELF'] == '/' ? '' : dirname($_SERVER['PHP_SELF']) ).'/'.$params['_script'];
        }
        if ( $params['_script'] == $_SERVER['PHP_SELF'] ) {
          $params['_script'] = '';
        }
      } else {
        $params['_script'] = '';
      }

      $params['_type'] = $default_type;
      $ret = smarty_function_query($params, $smarty);

      if ( $params['_tag'] == 'y' ) {

        if ( $params['_ajax'] == 'y' && $params['_script'] == '' ) {
          require_once $smarty->_get_plugin_filepath('block', 'ajax_href');
          if ( ! isset($params['_htmlelement']) ) $params['_htmlelement'] = 'tiki-center';
          if ( ! isset($params['_template']) ) $params['_template'] = basename($_SERVER['PHP_SELF'], '.php').'.tpl';
          if ( ! file_exists('templates/'.$params['_template']) || $params['_template'] == 'noauto' ) {
            $params['_htmlelement'] = '';
            $params['_template'] = '';
          }
          $ret = smarty_block_ajax_href(
            array('template' => $params['_template'], 'htmlelement' => $params['_htmlelement']),
            $ret,
            $smarty,
            false
          );
        } else {
          $ret = 'href="'.$ret.'"';
        }

        if ( isset($params['_icon']) ) {
          if ( ! isset($params['_title']) && $content != '' ) $params['_title'] = $content;
          require_once $smarty->_get_plugin_filepath('function', 'icon');

          $icon_params = array('_id' => $params['_icon'], '_type' => $default_icon_type);
          if ( isset($params['_alt']) ) {
            $icon_params['alt'] = $params['_alt'];
          } elseif ( isset($params['_title']) ) {
            $icon_params['alt'] = $params['_title'];
            $icon_params['title'] = ''; // will already be included in the surrounding A tag
          }

          if ( isset($params['_menu_text']) && $params['_menu_text'] == 'y' ) {
            $icon_params['_menu_text'] = $params['_menu_text'];
            $icon_params['title'] = $params['_title']; // Used as the menu text
            $params['_title'] = ''; // will already be displayed as the menu text
          }
          if ( isset($params['_menu_icon']) ) $icon_params['_menu_icon'] = $params['_menu_icon'];
          if ( isset($params['_icon_class']) ) $icon_params['class'] = $params['_icon_class'];

          $content = smarty_function_icon($icon_params, $smarty);
        }

        $link = ( ( isset($params['_class']) && $params['_class'] != '' ) ? 'class="'.$params['_class'].'" ' : '' )
              . ( ( isset($params['_title']) && $params['_title'] != '' ) ? 'title="'.str_replace('"','\"',$params['_title']).'" ' : '' )
              . $ret;

        $ret = "<a $link>".$content.'</a>';

        if ( $params['_sort_field'] != '' ) {
          require_once $smarty->_get_plugin_filepath('function', 'show_sort');
          $ret .= "<a $link style='text-decoration:none;'>".smarty_function_show_sort(
            array('sort' => $params['_sort_arg'], 'var' => $params['_sort_field']),
            $smarty
          ).'</a>';
        }
      }
    } else {
      $params = array('_type' => $default_type);
      $ret = smarty_function_query($params, $smarty);
    }

    return $ret;
}

?>
