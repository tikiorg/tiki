{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}User Workspaces{/tr}" name="aulawiki_user_workspaces" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
<ul class="myworkspaces">
{foreach from=$userWorkspaces item=userWorkspace}
 <li class="myworkspaceNoActive"> 
 <a class="link" href="aulawiki-workspace_desktop.php?workspaceId={$userWorkspace.workspaceId}">({$userWorkspace.code}) {$userWorkspace.name}</a>
 </li>
{/foreach}
<ul>
{/tikimodule}
