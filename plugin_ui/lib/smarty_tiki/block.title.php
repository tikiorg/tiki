<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_block_title : add a title to a template.
 *
 * params: 
 *    help: name of the doc page on doc.tw.org
 *    admpage: admin panel name
 *    url: link on the title
 *
 * usage: {title help='Example' admpage='example'}{tr}Example{/tr}{/title}
 *
 */

function smarty_block_title($params, $content, &$smarty, $repeat)
{
  global $prefs;
  global $smarty;
  global $tiki_p_view_templates, $tiki_p_edit_templates, $tiki_p_admin;

  if ( $repeat || $content == '' ) return;

  if ( ! isset($params['help']) ) $params['help'] = '';
  if ( ! isset($params['admpage']) ) $params['admpage'] = '';
  if ( ! isset($params['url']) ) $params['url'] = $_SERVER['REQUEST_URI'];
  
  $html = '<h1>';
  $html .= '<a class="pagetitle" href="' . $params['url'] . '">' . $content . "</a>\n";
  
  if ( $prefs['feature_help'] == 'y' && $prefs['helpurl'] != '' && $params['help'] != '' ) {
    $html .= '<a href="' . $prefs['helpurl'] . $params['help'] . '" target="tikihelp" class="tikihelp" title="' . tra('Help page:') . ' ' . $content . '">'
          . smarty_function_icon(array('_id' => 'help') , $smarty)
          . "</a>\n";
  }
  
  if ( $prefs['feature_view_tpl'] == 'y' && ( $tiki_p_edit_templates || $tiki_p_edit_templates ) ) {
    $html .= '<a href="tiki-edit_templates.php?template=' . $smarty->_tpl_vars['mid'] . '" target="tikihelp" class="tikihelp" title="' . tra('View tpl:') . ' ' . $content . '">' 
          . smarty_function_icon(array('_id' => 'shape_square_edit', 'alt' => tra('Edit Template')), $smarty)
          . "</a>\n";
  }
  
  if ( $tiki_p_admin == 'y' && $params['admpage'] != '' ) {
    $html .= '<a href="tiki-admin.php?page=' . $params['admpage'] . '">'
          . smarty_function_icon(array('_id' => 'wrench', 'alt' => tra('Admin Feature')), $smarty)
          . "</a>\n";
  }
  
  $html .= '</h1>';

  return $html;

}



?>
