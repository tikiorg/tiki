{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}Child Workspaces{/tr}" name="aulawiki_child_workspaces" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
</BR>
{foreach from=$workspaces item=userWorkspace}
{if $userWorkspace.isuserws!="y"}
 <img src='images/aulawiki/edu_workspace.png' align="middle" border='0' alt='{tr}Inactive workspace{/tr}' title='{tr}Inactive workspace{/tr}' />
 <a class="link" href="aulawiki-workspace_desktop.php?workspaceId={$userWorkspace.workspaceId}">({$userWorkspace.code}) {$userWorkspace.name}</a>
 <br/>
 {/if}
{/foreach}
<b>Personal workspaces:</b>
<a id="flipperchildPersonalWS" class="link" href="javascript:flipWithSign('childPersonalWS')">[+]</a>
<div id="childPersonalWS" style="display:none;" class="myworkspaces">
{foreach from=$workspace_users item=wsuser}
 <img src='images/aulawiki/edu_workspace.png' align="middle" border='0' alt='{tr}Inactive workspace{/tr}' title='{tr}Inactive workspace{/tr}' />
 <a class="link" href="aulawiki-workspace_desktop.php?workspaceId={$currentWorkspace.workspaceId}&wsuser={$wsuser.login}">({$wsuser.login}){$wsuser.name}</a>
 <br/>
{/foreach}
</div>
{/tikimodule}