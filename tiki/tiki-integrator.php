<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-integrator.php,v 1.1 2003-10-13 17:17:49 zaufi Exp $
 *
 * Doxygened files viewer (wrapper)
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');

$repID = isset($_REQUEST["repID"]) ? $_REQUEST["repID"] : 0;

if (!isset($_REQUEST["repID"]) && ($repID <= 0))
{
    $smarty->assign('msg',tra("No repository given"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
// Get repository configuration data
$rep = $integrator->get_repository($repID);

// Check if given file present at configured location
$file = $integrator->get_rep_file($rep, isset($_REQUEST["file"]) ? $_REQUEST["file"] : '');
if (!file_exists($file))
{
    $smarty->assign('msg',tra("File not found"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

// Get file content to string
$data = file_get_contents($file);

// Now we need to hack this file by applying all configured rules...
$rules = $integrator->list_rules($repID);
if (is_array($rules)) foreach ($rules as $rule) $data = $integrator->apply_rule($rep, $rule, $data);

// Display the template
$smarty->assign_by_ref('data', $data);
$smarty->assign('mid','tiki-integrator.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>