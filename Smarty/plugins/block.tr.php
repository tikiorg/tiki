<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.translate.php
 * Type:     block
 * Name:     translate
 * Purpose:  translate a block of text
 * -------------------------------------------------------------
 */
global $lang;
//include_once('lang/language.php');
function smarty_block_tr($params, $content, &$smarty)
{
    global $lang;
    if ($content) {
      if(isset($lang[$content])) {
        echo $lang[$content];  
      } else {
        echo $content;        
      }
    }
}
?>