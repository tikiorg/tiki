<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-admin_integrator_rules.php,v 1.3 2003-10-14 22:49:10 zaufi Exp $
 *
 * Admin interface for rules management
 *
 */

require_once('tiki-setup.php');
require_once('lib/integrator/integrator.php');
// Setup local variables from request or set default values
$repID       = isset($_REQUEST["repID"])       ? $_REQUEST["repID"]       :  0;
$ruleID      = isset($_REQUEST["ruleID"])      ? $_REQUEST["ruleID"]      :  0;
$srcrep      = isset($_REQUEST["srcrep"])      ? $_REQUEST["srcrep"]      :  0;
$srch        = isset($_REQUEST["srch"])        ? $_REQUEST["srch"]        : '';
$repl        = isset($_REQUEST["repl"])        ? $_REQUEST["repl"]        : '';
$description = isset($_REQUEST["description"]) ? $_REQUEST["description"] : '';
$rxmod       = isset($_REQUEST["rxmod"])       ? $_REQUEST["rxmod"]       : '';
$file        = isset($_REQUEST["file"])        ? $_REQUEST["file"]        : '';
$type        = isset($_REQUEST["type"])        ? ($_REQUEST["type"]      == 'on' ? 'y' : 'n')  : 'n';
$casesense   = isset($_REQUEST["casesense"])   ? ($_REQUEST["casesense"] == 'on' ? 'y' : 'n')  : 'n';
$code        = isset($_REQUEST["code"])        ? ($_REQUEST["code"]      == 'on' ? 'y' : 'n')  : 'n';
$html        = isset($_REQUEST["html"])        ? ($_REQUEST["html"]      == 'on' ? 'y' : 'n')  : 'n';

if (!isset($_REQUEST["repID"]) || $repID <= 0)
{
    $smarty->assign('msg',tra("No repository"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
// Check if copy button pressed
if (isset($_REQUEST["copy"]) && ($srcrep > 0))
    $integrator->copy_rules($srcrep, $repID);

// Check if 'save' button pressed ...
if (isset($_REQUEST["save"]))
{
    // ... and all mandatory paramaters r OK
    if (strlen($srch)  > 0)
        $integrator->add_replace_rule($repID, $ruleID, $srch, $repl, $type, $casesense, $rxmod, $description);
    else
    {
        $smarty->assign('msg',tra("Search is mandatory field"));
        $smarty->display("styles/$style_base/error.tpl");
        die;
    }
}
// Check if 'preview' button pressed ...
if (isset($_REQUEST["preview"]))
{
    // Prepeare rule data
    $rule = array();
    $rule["repID"] = $repID;
    $rule["ruleID"] = $ruleID;
    $rule["srch"] = $srch;
    $rule["repl"] = $repl;
    $rule["type"] = $type;
    $rule["casesense"] = $casesense;
    $rule["rxmod"] = $rxmod;
    $rule["description"] = $description;

    // Reassign values in form
    $smarty->assign('ruleID', $rule["ruleID"]);
    $smarty->assign('srch', $rule["srch"]);
    $smarty->assign('repl', $rule["repl"]);
    $smarty->assign('type', $rule["type"]);
    $smarty->assign('casesense', $rule["casesense"]);
    $smarty->assign('rxmod', $rule["rxmod"]);
    $smarty->assign('description', $rule["description"]);

    // Have smth to show?
    if (($html != 'y' || $code != 'y'))
    {
        // Get repository configuration data
        $rep = $integrator->get_repository($repID);
        // Check if file given and present at configured location
        $f = $integrator->get_rep_file($rep, $file);
        if (!file_exists($f))
        {
            $smarty->assign('msg',tra("File not found"));
            $smarty->display("styles/$style_base/error.tpl");
            die;
        }
        // Get file content to string
        $data = file_get_contents($f);
        // Apply rule
        $data = $integrator->apply_rule($rep, $rule, $data);
        $smarty->assign_by_ref('preview_data', $data);
    }
}

// Whether some action requested?
if (isset($_REQUEST["action"]))
{
    switch ($_REQUEST["action"])
    {
    case 'edit':
        if ($ruleID != 0)
        {
            $rule = $integrator->get_rule($ruleID);
            $smarty->assign('ruleID', $rule["ruleID"]);
            $smarty->assign('srch', $rule["srch"]);
            $smarty->assign('repl', $rule["repl"]);
            $smarty->assign('type', $rule["type"]);
            $smarty->assign('casesense', $rule["casesense"]);
            $smarty->assign('rxmod', $rule["rxmod"]);
            $smarty->assign('description', $rule["description"]);
        }
        break;
    case 'rm':
        if ($ruleID != 0) $integrator->remove_rule($ruleID);
        break;
    default:
        $smarty->assign('msg', tra("Requested action in not supportted on repository"));
        $smarty->display("styles/$style_base/error.tpl");
        die; break;
    }
}
// Get repository name
$r = $integrator->get_repository($repID);
$smarty->assign('name', $r["name"]);

// Reassign checkboxes
$smarty->assign('file', $file);
$smarty->assign('code', $code);
$smarty->assign('html', $html);

// Fill list of rules
$rules = $integrator->list_rules($repID);
$smarty->assign_by_ref('rules', $rules);
$smarty->assign('repID', $repID);

// Fill list of possible source repositories
$allreps = $integrator->list_repositories(false);
$reps = array();
foreach($allreps as $rep) $reps[$rep["repID"]] = $rep["name"];
$smarty->assign_by_ref('reps', $reps);

// Display the template
$smarty->assign('mid','tiki-admin_integrator_rules.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>