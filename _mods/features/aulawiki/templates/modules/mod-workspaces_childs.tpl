{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title="{tr}Child Workspaces{/tr}" name="workspaces_childs" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
</BR>
{foreach from=$workspaces item=userWorkspace}
{if $userWorkspace.isuserws!="y"}
 <img src='images/workspaces/edu_workspace.png' align="middle" border='0' alt='{tr}Inactive workspace{/tr}' title='{tr}Inactive workspace{/tr}' />
 <a class="link" href="tiki-workspaces_desktop.php?workspaceId={$userWorkspace.workspaceId}">({$userWorkspace.code}) {$userWorkspace.name}</a>
 <br/>
 {/if}
{/foreach}
{if $workspace_users}
	<b>Personal workspaces:</b>
	<a id="flipperchildPersonalWS" class="link" href="javascript:flipWithSign('childPersonalWS')">[+]</a>
	<div id="childPersonalWS" style="display:none;" class="myworkspaces">
	{foreach from=$workspace_users item=wsuser}
	 <img src='images/workspaces/edu_workspace.png' align="middle" border='0' alt='{tr}Inactive workspace{/tr}' title='{tr}Inactive workspace{/tr}' />
	 <a class="link" href="tiki-workspaces_desktop.php?workspaceId={$currentWorkspace.workspaceId}&wsuser={$wsuser.login}">({$wsuser.login}){$wsuser.name}</a>
	 <br/>
	{/foreach}
	</div>
{/if}
{/tiki_workspaces_module}