<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/Smarty/plugins/function.var_dump.php,v 1.2 2003-08-01 10:30:45 redflo Exp $
 *
 * \author zaufi <zaufi@sendmail.ru>
 */


/**
 * \brief Smarty plugin to add variable dump to debug console log
 * Usage format {var_dump var=var_name_2_dump}
 */
function smarty_function_var_dump($params, &$smarty)
{
  global $debugger;
  require_once('lib/debug/debugger.php');
  //
  $v = $params['var'];
  if (strlen($v) != 0)
  {
    $tmp = $smarty->get_template_vars();
    if (is_array($tmp) && isset($tmp[$v]))
      $debugger->msg("Smarty var_dump(".$v.') = '.print_r($tmp[$v], true));
    else
      $debugger->msg("Smarty var_dump(".$v."): Variable not found");
  }
  else
    $debugger->msg("Smarty var_dump: Parameter 'var' not specified");
  return '<!-- var_dump('.$v.') -->';
}

?>
