{* $Id$ *}
{if isset($public_tasks)}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href='tiki-user_tasks.php'>{tr}Public Tasks{/tr}</a>"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="user_tasks_public" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<table class="normal">
<tr><td width="100%">
 <form class="forms" method="post" action="{$ownurl}">
    <select name="user_group">
    <option value="">{tr}select{/tr}</option>
{section name=ix loop=$user_groups}
    <option value="{$user_groups[ix].groupName|escape}" {if $user_groups[ix].groupName eq $user_group}
       selected="selected"
    {/if}>{tr}{$user_groups[ix].groupName|escape}{/tr}
</option>
{/section}
    </select>
    <input type="submit" class="wikiaction" name="modTasksSearch" value="{tr}Go{/tr}" /> 
</form>
</td></tr>
{section name=iix loop=$public_tasks}
<tr><td class="prio{$public_tasks[iix].priority}">
{if isset($modTasks[ix].percentage)}({$public_tasks[iix].percentage}%) {/if}{$public_tasks[iix].taskId|tasklink:linkmodule}
<br />{$public_tasks[iix].user|username}</td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
{/tikimodule}
{/if}
