<?php
function smarty_function_elapsed($params, &$smarty)
{
    global $tiki_timer;
    
    $ela = $tiki_timer->elapsed();
    print($ela);
}

/* vim: set expandtab: */

?>
