{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}My Workspaces{/tr}" name="workspaces_my" flip=$module_params.flip decorations=$module_params.decorations}
{tr}Current workspace:{/tr}<br/>
{foreach from=$selectedWorkspaces item=selectedWorkspace}
{if $activeWorkspace.workspaceId==$selectedWorkspace.workspaceId}
    <img src='images/workspaces/edu_workspaceActive.png' align="middle" border='0' alt='{tr}Active workspace{/tr}:({$selectedWorkspace.code}) {$selectedWorkspace.name}' title='{tr}Active workspace{/tr}:({$selectedWorkspace.code}) {$selectedWorkspace.name}' />
    <a class="linkMyworkspaceActive" href="tiki-workspaces_desktop.php?workspaceId={$selectedWorkspace.workspaceId}">{$selectedWorkspace.name}</a>
 <a id="flipperidwsmenu{$selectedWorkspace.workspaceId}" class="link" href="javascript:flipWithSign('idwsmenu{$selectedWorkspace.workspaceId}')">[-]</a>
<div id="idwsmenu{$selectedWorkspace.workspaceId}" >
 {workspaces_menu id=$selectedWorkspace.type.menuid workspaceId=$selectedWorkspace.workspaceId}
</div>

{else}
	<img src='images/workspaces/edu_workspaceInactive.png' align="middle" border='0' alt='{tr}Active workspace{/tr}:({$selectedWorkspace.code}) {$selectedWorkspace.name}' title='{tr}Active workspace{/tr}:({$selectedWorkspace.code}) {$selectedWorkspace.name}' />
    <a class="link" href="tiki-workspaces_desktop.php?workspaceId={$selectedWorkspace.workspaceId}">{$selectedWorkspace.name}</a>
 <a id="flipperidwsmenu{$selectedWorkspace.workspaceId}" class="link" href="javascript:flipWithSign('idwsmenu{$selectedWorkspace.workspaceId}')">[+]</a>
<div id="idwsmenu{$selectedWorkspace.workspaceId}" style="display:none;">
 {workspaces_menu id=$selectedWorkspace.type.menuid workspaceId=$selectedWorkspace.workspaceId}
</div>

{/if}
<BR/>
{/foreach}
<BR/>
{tr}My workspaces:{/tr}<br/>
<div class="myworkspaces">
{foreach from=$userWorkspaces item=userWorkspace}
 <img src='images/workspaces/edu_workspaceInactive.png' align="middle" border='0' alt='{tr}Inactive workspace{/tr}:({$userWorkspace.code}) {$userWorkspace.name}' title='{tr}Inactive workspace{/tr}:({$userWorkspace.code}) {$userWorkspace.name}' /> 
 <a class="link" href="tiki-workspaces_desktop.php?workspaceId={$userWorkspace.workspaceId}">{$userWorkspace.name}</a>
<br/>
{/foreach}
</div>
{/tikimodule}
