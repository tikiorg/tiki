{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title="{tr}$title{/tr}" name="workspaces_owner" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}

{if $showName!="n"}
	<div>
	<a href="./tiki-user_information.php?view_user={$owner}">
	<img src="./tiki-show_user_avatar.php?user={$owner}" align='absmiddle' border=0>
	({$owner}) {$userPreferences.realName}
	</a>
	</div>
{/if}
{if $showWorkspaces!="n"}
	<div class="myworkspaces">
		<ul class="workspaceList">
		{foreach from=$userWorkspaces item=userWorkspace}
		 <li class="myworkspaceNoActive"> 
		 <a class="link" href="tiki-workspaces_desktop.php?workspaceId={$userWorkspace.workspaceId}">({$userWorkspace.code}) {$userWorkspace.name}</a>
		 </li>
		{/foreach}
		<ul>
	</div>
{/if}
{/tiki_workspaces_module}
