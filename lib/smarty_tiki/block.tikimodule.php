<?php

/**
 * \brief Smaty {tikimodule}{/tikimodule} block handler
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {tikimodule name="mandatoty_module_name" title="Module title"}
 *    <!-- module Smarty/HTML code here -->
 *  {/tikimodule}
 * \endcode
 *
 * This block may (can) use 2 Smarty templates:
 *  1) module.tpl = usual template to generate module look-n-feel
 *  2) module-error.tpl = to generate diagnostic error message about
 *     incorrect {tikimodule} parameters
 */
function smarty_block_tikimodule($params, $content, &$smarty)
{
    extract($params);
    $tpl = 'module.tpl';
    
    // Check args
    if (isset($title)) $smarty->assign('module_title', $title);
    if (isset($name)) $smarty->assign('module_name', $name);
    else $tpl = 'module-error.tpl';
    $smarty->assign_by_ref('module_content', $content);

    return $smarty->fetch($tpl);
}
?>
