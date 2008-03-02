{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-user_tasks.tpl,v 1.18 2007/10/14 17:51:03 mose *}
{if $prefs.feature_tasks eq 'y' and $user}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href='tiki-user_tasks.php'>{tr}User tasks{/tr}</a>"}{/if}
{tikimodule title=$tpl_module_title name="user_tasks" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<div class="module">
<form action="{$ownurl}" method="post">
<input style="font-size: 9px;" type="text" name="modTasksTitle" />
<input style="font-size: 9px;" type="submit" name="modTasksSave" value="{tr}add{/tr}" />
</form>
</div>
<form action="{$ownurl}" method="post">
{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modTasks}
		<li class="prio{$modTasks[ix].priority}">
			{if $modTasks[ix].creator ne $user } 
			&gt;&gt; 
			{else}
			<input  type="checkbox" name="modTasks[{$modTasks[ix].taskId}]" />
			{/if}
			{$modTasks[ix].taskId|tasklink:linkmodule} ({$modTasks[ix].percentage}%)
		</li>
{*{sectionelse}
	<div class="module">&nbsp;</div>
{/section}*}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
<input style="font-size: 9px;" type="submit" name="modTasksCom" value="{tr}done{/tr}" />
<input style="font-size: 9px;" type="submit" name="modTasksDel" value="{tr}del{/tr}" />
</form>
{/tikimodule}
{/if}
