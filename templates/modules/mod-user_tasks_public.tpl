{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_tasks_public.tpl,v 1.2 2005-01-22 22:56:27 mose Exp $ *}
{if $feature_tasks eq 'y' and $user}
{tikimodule title="<a class='cboxtlink' href='tiki-user_tasks_public.php'>{tr}Public tasks{/tr}</a>" name="user_tasks_public"}
<table  border="0" cellpadding="0" cellspacing="0" width="100%">
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
    <input type="submit" class="wikiaction" name="modTasksSearch" value="{tr}go{/tr}" /> 
</form>
</td></tr>
{section name=iix loop=$public_tasks}
<tr><td class="prio{$public_tasks[iix].priority}">
{* <tr><td class="module"> *}
({$public_tasks[iix].percentage}%)
{$public_tasks[iix].taskId|tasklink:linkmodule}
<br>{$public_tasks[iix].user}</td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
{/tikimodule}
{/if}
