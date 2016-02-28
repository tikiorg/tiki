{title help="User+Tasks"}{tr}Tasks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="t_navbar">
	{button href="tiki-user_tasks.php?show_form=y" _class="btn btn-default" _text="{tr}New task{/tr}"}
	{button href="tiki-user_tasks.php" _class="btn btn-default" _text="{tr}Task list{/tr}"}

	{if $task_admin}
		{if $admin_mode}
			{button href="tiki-user_tasks.php?taskId=$taskId&amp;admin_mode_off=on" _text="{tr}Admin mode off{/tr}"}
		{else}
			{button href="tiki-user_tasks.php?taskId=$taskId&amp;admin_mode=on" _text="{tr}Admin mode on{/tr}"}
		{/if}
	{/if}

	{button href="#" _onclick="javascript:flip('edithelpzone'); return false;" _text="{tr}Task help{/tr}"}

	{if (not $show_form)}
		{button href="tiki-user_tasks.php?emty_trash=Empty trash" _class="btn btn-default" _text="{tr}Empty Trash{/tr}"}
	{/if}
</div>

{* start ************ Search ***************}
{if (not $show_form)}
	{include file='find.tpl'}
{/if}
{if $cant eq 1}
	<span class="taskcount">{$cant}&nbsp;{tr}Task{/tr}</span>
{else}
	<span class="taskcount">{$cant}&nbsp;{tr}Tasks{/tr}</span>
{/if}

<div class="wiki-edithelp" id='edithelpzone' >
	<table class="table norml table-striped table-hover">
		{if $tiki_p_tasks_receive eq 'y'}
			<tr>
				<td>
					{icon name='login' class='tips' title=":{tr}Task received{/tr}"}
				</td>
				<td>
					{tr}You received this task{/tr}.
				</td>
			</tr>
		{/if}
		{if $tiki_p_tasks_send eq 'y'}
			<tr>
				<td>
					{icon name='logout' class='tips' title=":{tr}Task sent{/tr}"}
				</td>
				<td>
					{tr}You sent this task to another user{/tr}.
				</td>
			</tr>
		{/if}
		{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}
			<tr>
				<td>
					{icon name='group' class='tips' title=":{tr}Task shared by a group{/tr}"}
				</td>
				<td>
					{tr}Task is shared by a group{/tr}.
				</td>
			</tr>
		{/if}
		<tr>
			<td>
				{icon name='ok' class='tips' title=":{tr}Accepted by task user and creator{/tr}"}
			</td>
			<td>
				{tr}Task has been accepted by user and creator{/tr}.
			</td>
		</tr>
		<tr>
			<td>
				{icon name='remove' class='tips' title=":{tr}Rejected by a user{/tr}"}
			</td>
			<td>
				{tr}Task has been rejected by a user{/tr}.
			</td>
		</tr>
		{if $tiki_p_tasks_receive eq 'y'}
			<tr>
				<td>
					{icon name='history' class='tips' title=":{tr}Waiting for me{/tr}"}
				</td>
				<td>
					{tr}Task has not yet been accepted or rejected by you{/tr}.
				</td>
			</tr>
		{/if}
		{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}
			<tr>
				<td>
					{icon name='user' class='tips' title=":{tr}Waiting for other user{/tr}"}
				</td>
				<td>
					{tr}Task has not yet been accepted or rejected by another user{/tr}.
				</td>
			</tr>
		{/if}
	</table>
</div>
<br>

{if $admin_mode}<div align="center"><a class="highlight" >{tr}Admin Mode{/tr}</a></div><br>{/if}
{* start ************ view ***************}
{if (($tiki_view_mode eq 'view') or ($tiki_view_mode eq 'preview'))}
	{include file='tiki-user_tasks_view.tpl'}
{/if}

{* end ************ View task ***************}

{* start ************ Edit Form ***************}

{if ($show_form)}
	{include file='tiki-user_tasks_edit.tpl'}
{/if}

{* end ************ Edit Form ***************}

{* start ************ Task list ***************}
{if ( not $show_form )}
	{include file='tiki-user_tasks_list.tpl'}
{/if}

{* end ************ Task list ***************}

<br>
<br>
{* start ************ Search ***************}
