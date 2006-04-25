<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
include_once("lib/init/initlib.php");
require_once('tiki-setup_base.php');

require_once('lib/aulawiki/assignmentslib.php');
require_once('lib/aulawiki/workspacelib.php');
require_once ('lib/aulawiki/periodslib.php');
include_once ("lib/docbook/ziplib.php");

$workspacesLib = new WorkspaceLib($dbTiki);
$assignmentsLib = new AssignmentsLib($dbTiki);

if ( isset($_REQUEST["workspaceId"]) ){
	$workspace = $workspacesLib->get_workspace_by_id($_REQUEST["workspaceId"]);
}elseif(isset($_SESSION["currentWorkspace"])){
	$workspace = $_SESSION["currentWorkspace"];
}


if(isset($_REQUEST["send"])) {

}else if(isset($_REQUEST["edit"])) {
}

$periodsLib = new PeriodsLib($dbTiki);
//TODO: asociar al workspace un tipo de periodo y leer los ese tipo
$periods = $periodsLib->get_periods_of_type(1);
$smarty->assign("periods",$periods);

$assignments = $assignmentsLib->get_assignments('startDate_desc', $workspace["workspaceId"]);
$gradebook = $assignmentsLib->get_workspace_gradebook($workspace["workspaceId"]);

$smarty->assign_by_ref('gradebook', $gradebook);
$smarty->assign_by_ref('assignments', $assignments);

$content = $smarty->fetch('aulawiki-view_gradebook_xml.tpl');

//create sxw zip file
$ziptmp = new ZipWriter("", "GradeBook-".$workspace["workspaceId"].".zip");
$ziptmp->addRegularFile("content.xml", $content, false);

$contentzip = $ziptmp->finish();
header("Content-type: application/x-zip-compressed");
header( "Content-Disposition: attachment; filename=/""."GradeBook-".$workspace["workspaceId"].".sxc/"" );
//header( "Content-Disposition: attachment; filename=/""."GradeBook-1.zip/"" );

header("Content-Length: ". strlen($contentzip) );
////////////////////////////////////////////////////////////////////////////
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
echo $contentzip;
?>