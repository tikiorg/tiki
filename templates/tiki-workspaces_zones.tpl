{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{php}
require_once('lib/workspaces/workspacelib.php');
require_once('lib/workspaces/typeslib.php');
global $dbTiki;
$workspacesLib = new WorkspaceLib($dbTiki);
$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (isset($workspace) && $workspace!=null) {
	$solapas = array();
	global $user;
	if(isset($workspace["isuserws"]) && $workspace["isuserws"]=="y"){
		$parentWorkspace = $workspacesLib->get_workspace_by_id($workspace["parentId"]);
		//print_r($parentWorkspace);
		$solapa1 = array();
		$solapa1["name"] = $parentWorkspace["name"];
		$solapa1["url"] = "./tiki-workspaces_desktop.php?workspaceId=".$parentWorkspace["workspaceId"];
		$solapa1["active"] = "n";
		$solapas[] = $solapa1;
		if($workspace["code"] != "LF".$parentWorkspace["code"].$user){
			$solapa3 = array();
			$solapa3["name"] = $user." ".tr("personal workspace");
			$solapa3["url"] = "./tiki-workspaces_desktop.php?workspaceId=".$parentWorkspace["workspaceId"]."&wsuser=".$user;
			$solapa3["active"] = "n";
			$solapas[] = $solapa3;
		}
		$solapa2 = array();
		$solapa2["name"] = $workspace["name"];
		$solapa2["url"] = "./tiki-workspaces_desktop.php?workspaceId=".$workspace["workspaceId"];
		$solapa2["active"] = "y";
		$solapas[] = $solapa2; 
	}else{ // no se muestra un ws de usuario
		//$wstype = $wsTypesLib->get_workspace_type_by_id($workspace["type"]);
		$wstype = $workspace["type"];
		$solapa1 = array();
		$solapa1["name"] = $workspace["name"];
		$solapa1["url"] = "./tiki-workspaces_desktop.php?workspaceId=".$workspace["workspaceId"];
		$solapa1["active"] = "y";
		$solapas[] = $solapa1;
		if (isset($wstype["userwstype"]) && $wstype["userwstype"]!="" && $wstype["userwstype"]!=0){ //se admiten ws de usuario
			$solapa3 = array();
			$solapa3["name"] = $user." ".tra("personal workspace");
			$solapa3["url"] = "./tiki-workspaces_desktop.php?workspaceId=".$workspace["workspaceId"]."&wsuser=".$user;
			$solapa3["active"] = "n";
			$solapas[] = $solapa3;
		}
	}
	global $smarty; 
	$smarty->assign('solapas',$solapas);
}
{/php}
{foreach key=key item=solapa from=$solapas}
	{if $solapa.active eq "y"}
		<div class="activetabbut">
		<img src='images/workspaces/edu_workspaceActive.png' align="middle" border='0'/>

		<a href="{$solapa.url}" class="activetablink">{$solapa.name}</a>
	{else}
		<div class="noactivetabbut">
		<img src='images/workspaces/edu_workspaceInactive.png' align="middle" border='0'/>

		<a href="{$solapa.url}" class="noactivetablink">{$solapa.name}</a>
	{/if}
</div>
{/foreach}