{if $feature_tasks eq 'y' and $user}
<div class="box">
<div class="box-title">
{tr}<a class="linkmodule" href="tiki-user_tasks.php">User tasks</a>{/tr}
</div>
<div class="box-data">
<form action="{$ownurl}" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTasks}
<tr><td class="module">
<input type="checkbox" name="modTasks[{$modTasks[ix].taskId}]" />
<a {if $modTasks[ix].status eq 'c'}style="text-decoration:line-through;"{/if} class="linkmodule" href="tiki-user_tasks.php?taskId={$modTasks[ix].taskId}">{$modTasks[ix].title}</a> ({$modTasks[ix].percentage}%)</td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
<input type="submit" name="modTasksCom" value="{tr}done{/tr}" />
<input type="submit" name="modTasksDel" value="{tr}del{/tr}" />
</form>
<form action="{$ownurl}" method="post">
<input type="text" name="modTasksTitle" />
<input type="submit" name="modTasksSave" value="{tr}add{/tr}" />
</form>
</div>
</div>
{/if}