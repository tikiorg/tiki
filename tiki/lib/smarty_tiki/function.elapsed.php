<?php
function smarty_function_elapsed($params, &$smarty)
{
    global $tiki_timer;
    
    $ela = number_format($tiki_timer->elapsed(),2);
    print($ela);
}

/* vim: set expandtab: */

?>
