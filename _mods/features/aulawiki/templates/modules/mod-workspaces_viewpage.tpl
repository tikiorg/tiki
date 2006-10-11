{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}View Page{/tr}"}
{/if}
{tiki_workspaces_module title="$title" name="workspaces_viewpage" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
{if $showBar!='n'}
	<div class="navigationbar">
		<div class="navigationOption">
			<form method="post" action="{$ownurl}">
			<input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
			<select name="name" id="name">
			{foreach key=key item=history_item from=$module_history}
			<option value="{$history_item}">{$history_item}</option>
			{/foreach}
			</select>
			<input class="edubutton" type="submit" name="goback" value="{tr}Go back{/tr}">
			</form>
		</div>
		<div class="navigationOption">
			<form method="post" action="{$ownurl}">
			<input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
			<input name="name" type="text" id="name" value="{$pageName}" size="20" maxlength="150">
			<input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
			</form>
		</div>
		<div class="navigationOption">
			<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$pageName}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a>
		</div>
	</div>
	{if $likepages}
		<form method="post" action="{$ownurl}">
		<input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
		{tr}Perhaps you were looking for:{/tr}
		<select name="name" id="name">
		{foreach key=key item=pagename from=$likepages}
		<option value="{$pagename}">{$pagename}</option>
		{/foreach}
		</select>
		<input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
		</form>
	{/if}
{/if}
<div>{$pageBody}
</div>
{/tiki_workspaces_module}
