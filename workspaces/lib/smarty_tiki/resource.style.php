<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File:     resource.style.php
 * Type:     resource
 * -------------------------------------------------------------
 */
function smarty_resource_style_source($tpl_name, &$tpl_source, &$smarty)
{
	// Check if file exists in the style directory if not
	// check if file exists in the templates directory,
	// if not then fall
}

function smarty_resource_style_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    // do database call here to populate $tpl_timestamp.
    $sql = new SQL;
    $sql->query("select tpl_timestamp
                   from my_table
                  where tpl_name='$tpl_name'");
    if ($sql->num_rows) {
        $tpl_timestamp = $sql->record['tpl_timestamp'];
        return true;
    } else {
        return false;
    }
}

function smarty_resource_style_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_style_trusted($tpl_name, &$smarty)
{
    // not used for templates
}
?>

