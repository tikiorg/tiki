{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_tasks_public.tpl,v 1.8 2007-10-14 17:51:02 mose Exp $ *}
{if $prefs.feature_tasks eq 'y' and $user}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href='tiki-user_tasks.php'>{tr}Public tasks{/tr}</a>"}{/if}
{tikimodule title=$tpl_module_title name="user_tasks_public" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table class="normal">
<tr><td width="100%">
 <form class="forms" method="post" action="{$ownurl}">
    <select name="user_group">
    <option value="">{tr}select{/tr}</option>
{section name=ix loop=$user_groups}
    <option value="{$user_groups[ix].groupName}" {if $user_groups[ix].groupName eq $user_group}
       selected="selected"
    {/if} >{tr}{$user_groups[ix].groupName}{/tr}
</option>
{sectionelse}
{/section}
    </select>
    <input type="submit" class="wikiaction" name="modTasksSearch" value="{tr}Go{/tr}" /> 
</form>
</td></tr>
{section name=iix loop=$public_tasks}
<tr><td class="prio{$public_tasks[iix].priority}">
{* <tr><td class="module"> *}
({$public_tasks[iix].percentage}%)
{$public_tasks[iix].taskId|tasklink:linkmodule}
<br />{$public_tasks[iix].user}</td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
{/tikimodule}
{/if}
