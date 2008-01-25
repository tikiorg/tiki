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
 */
function smarty_block_self_link($params, $content, &$smarty) {
    global $prefs;
    $default_type = 'absolute_path';

    require_once $smarty->_get_plugin_filepath('function', 'query');

    if ( is_array($params) ) {
      if ( ! isset($content) ) $content = '';
      if ( ! isset($params['_ajax']) ) $params['_ajax'] = 'y';
      if ( ! isset($params['_tag']) ) $params['_tag'] = 'y';
      if ( ! isset($params['_sort_arg']) ) $params['_sort_arg'] = 'sort';
      if ( ! isset($params['_sort_field']) ) {
        $params['_sort_field'] = '';
      } elseif ( $params['_sort_arg'] != '' ) {
        $params[$params['_sort_arg']] = $params['_sort_field'].'_asc,'.$params['_sort_field'].'_desc';
      }

      $params['_type'] = $default_type;
      $ret = smarty_function_query($params, $smarty);

      if ( $params['_tag'] == 'y' ) {

        if ( $params['_ajax'] == 'y' ) {
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
            $smarty
          );
        } else {
          $ret = 'href="'.$ret.'"';
        }

	$link = ( isset($params['_class']) ? ' class='.$params['_class'] : '' ).' '.$ret;
        $ret = "<a $link>".$content.'</a>';
        if ( isset($params['_sort_field']) ) {
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
