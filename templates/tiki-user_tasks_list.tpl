{*Smarty template*}

{* start ************ Task list ***************}
<form action="tiki-user_tasks.php" method="post">
	<table class="formcolor">
		<tr>
			<td colspan="6">
				<div align="right">
					{tr}Tasks per page{/tr}
					<select name="tasks_maxRecords">
					<option value="-1" {if $prefs.tasks_maxRecords eq -1} selected="selected"{/if}>{tr}All{/tr}</option>
					<option value="2" {if $prefs.tasks_maxRecords eq 2} selected="selected"{/if}>2</option>
					<option value="5" {if $prefs.tasks_maxRecords eq 5} selected="selected"{/if}>5</option>
					<option value="10" {if $prefs.tasks_maxRecords eq 10} selected="selected"{/if}>10</option>
					<option value="20" {if $prefs.tasks_maxRecords eq 20} selected="selected"{/if}>20</option>
					<option value="30" {if $prefs.tasks_maxRecords eq 30} selected="selected"{/if}>30</option>
					<option value="40" {if $prefs.tasks_maxRecords eq 40} selected="selected"{/if}>40</option>
					<option value="50" {if $prefs.tasks_maxRecords eq 50} selected="selected"{/if}>50</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<th style="text-align:right;" >&nbsp;</th>
			<th><a href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></th>
			<th><a href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}Start{/tr}</a></th>
			<th><a href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}End{/tr}</a></th>
			<th style="text-align:right;">
				<a href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'priority_desc'}priority_asc{else}priority_desc{/if}">
					{tr}Priority{/tr}
				</a>
			</th>
			<th style="text-align:right;">
				<a href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'percentage_desc'}percentage_asc{else}percentage_desc{/if}">
					{tr}Completed{/tr}
				</a>
			</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=task_i loop=$tasklist}
			<tr>
				<td class="prio{$tasklist[task_i].priority}">
					<input {if $tasklist[task_i].disabled} disabled = "disabled" {/if}  type="checkbox" name="task[{$tasklist[task_i].taskId}]" />
					{if $tasklist[task_i].deleted} 
						{if $tasklist[task_i].creator ne $user}
							{icon _id='delete'}
						{else}
							{icon _id='basket' title="{tr}In the trash{/tr}" alt="{tr}In the trash{/tr}"}
						{/if}
					{/if}
					{if (($tasklist[task_i].creator eq $tasklist[task_i].user) and ($tasklist[task_i].user eq $user))}
						{*private task*}
					{else}
						{if ($tasklist[task_i].user eq $user)}
							{*received task*}
							{icon _id='door_in' title="{tr}Task received{/tr}" alt="{tr}Task received{/tr}"} 
							{if (($tasklist[task_i].accepted_creator eq 'n') or ($tasklist[task_i].accepted_user eq 'n'))}
								{icon _id='cross' title="{tr}Rejected by a user{/tr}" alt="{tr}Rejected by a user{/tr}"}
							{else}
								{if ($tasklist[task_i].accepted_user eq '')}
									{icon _id='hourglass' title="{tr}Waiting for me{/tr}" alt="{tr}Waiting for me{/tr}"}
								{else}
									{if ($tasklist[task_i].accepted_creator eq 'y')} 
										{icon _id='tick' title="{tr}Accepted by task user and creator{/tr}" alt="{tr}Accepted by task user and creator{/tr}"}
									{else}
										{icon _id='user_comment' title="{tr}Waiting for other user{/tr}" alt="{tr}Waiting for other user{/tr}"}
									{/if}
								{/if}
							{/if}
						{elseif ($tasklist[task_i].creator eq $user)}
							{*submitted task*}
							{icon _id='door_out' title="{tr}Task sent{/tr}" alt="{tr}Task sent{/tr}"} 
							{if (($tasklist[task_i].accepted_creator eq 'n') or ($tasklist[task_i].accepted_user eq 'n'))}
								<img src="{$img_not_accepted}" height="{$img_not_accepted_height}" width="{$img_not_accepted_width}" title="{tr}Not Accepted by One User{/tr}" alt="{tr}Not Accepted User{/tr}" />
							{else}
								{if ($tasklist[task_i].accepted_user eq '')}
									{if ($tasklist[task_i].accepted_creator eq 'y')}
										{icon _id='user_comment' title="{tr}Waiting for other user{/tr}" alt="{tr}Waiting for other user{/tr}"}
									{else}
										{icon _id='hourglass' title="{tr}Waiting for me{/tr}" alt="{tr}Waiting for me{/tr}"}
									{/if}
								{else}
									{if ($tasklist[task_i].accepted_creator eq 'y')}
										{icon _id='tick' title="{tr}Accepted by task user and creator{/tr}" alt="{tr}Accepted by task user and creator{/tr}"}
									{else}
										{icon _id='hourglass' title="{tr}Waiting for me{/tr}" alt="{tr}Waiting for me{/tr}"}
									{/if}
								{/if}
							{/if}
						{else}
							{*shared task*}
							{icon _id='group' title="{tr}Task shared by a group{/tr}" alt="{tr}Task shared by a group{/tr}"} 
						{/if}
					{/if}
				</td>
				<td class="prio{$tasklist[task_i].priority}">
					<a {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="link" href="tiki-user_tasks.php?taskId={$tasklist[task_i].taskId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;tiki_view_mode=view&amp;find={$find}">{$tasklist[task_i].title|escape}</a>
				</td>
				<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}">
					<div class="mini">
						{$tasklist[task_i].start|tiki_short_date}&nbsp;[{$tasklist[task_i].start|tiki_short_time}]
					</div>
				</td>
				<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}">
					<div class="mini">
						{$tasklist[task_i].end|tiki_short_date}&nbsp;[{$tasklist[task_i].end|tiki_short_time}]
					</div>
				</td>
				<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">
					{$tasklist[task_i].priority}
				</td>
				<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">
					<select {if $tasklist[task_i].disabled} disabled = "disabled" {/if}  name="task_perc[{$tasklist[task_i].taskId}]">
						<option value="w" {if $tasklist[task_i].percentage_null} selected = "selected"  {/if}>{tr}Waiting{/tr}</option>	
						{section name=zz loop=$percs}
							<option value="{$percs[zz]|escape}" {if $tasklist[task_i].percentage eq $percs[zz] and !$tasklist[task_i].percentage_null} selected = "selected" {/if} >
								{$percs[zz]}%
							</option>	
						{/section}
					</select>
				</td>
			</tr>
		{sectionelse}
			<tr>
				<td class="odd" colspan="6">{tr}No tasks entered{/tr}</td>
			</tr>
		{/section}
		<tr>
			<td colspan="3" style="text-align:left; vertical-align:bottom;">
				{icon _id='arrow_turn_down_right' title="{tr}Select{/tr}" alt="{tr}Select{/tr}" style="margin-bottom:8px; margin-left:5px"}
				<select name="action" style="vertical-align:bottom;">
					<option value="" >{tr}Select One{/tr}</option>
					<option value="waiting_marked" >{tr}Waiting{/tr}</option>
					<option value="open_marked" >{tr}Open{/tr}</option>
					<option value="complete_marked" >{tr}Completed{/tr}</option>
					<option value="move_marked_to_trash">{tr}Trash{/tr}</option>
					<option value="remove_marked_from_trash">{tr}Undo Trash{/tr}</option>
				</select>
				<input type="submit" name="update_tasks" value="{tr}Go{/tr}" style="vertical-align:bottom;" />
			</td>
			<td colspan="3" style="text-align:right;">
				<input type="submit" name="update_percentage" value="{tr}Go{/tr}" style="vertical-align:bottom;" />
				{icon _id='arrow_turn_down_left' title="{tr}Go{/tr}" alt="{tr}Go{/tr}" style="margin-bottom:8px; margin-right:8px"}
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:center;">
				&nbsp;&nbsp;{tr}Show:{/tr}
				&nbsp;<input  name="show_private" {if $show_private} checked="checked" {/if} type="checkbox" />{tr}Private{/tr}
			{if $tiki_p_tasks_receive eq 'y'}&nbsp;<input  name="show_received" {if $show_received} checked="checked" {/if} type="checkbox" />{tr}Received{/tr}{/if}
			{if $tiki_p_tasks_send eq 'y'}&nbsp;<input  name="show_submitted" {if $show_submitted} checked="checked" {/if} type="checkbox" />{tr}Submitted{/tr}{/if}
			{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}&nbsp;<input  name="show_shared" {if $show_shared} checked="checked" {/if} type="checkbox" />{tr}Shared{/tr}{/if}
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;<input  name="show_trash" {if $show_trash} checked="checked" {/if} type="checkbox" />{tr}Trash{/tr}
			&nbsp;<input  name="show_completed" {if $show_completed} checked="checked" {/if} type="checkbox" />{tr}Completed{/tr}
			{if ($admin_mode)}
			&nbsp;&nbsp;
			<a class="highlight" >
			<input name="show_admin" {if $show_admin} checked="checked" {/if} type="checkbox" />{tr}All Shared Tasks{/tr}</a>
			{/if}
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:center;">
				<input type="submit" name="reload" value="{tr}Reload{/tr}" />
			</td>
		</tr>
	</table>
</form>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{* end ************ Task list ***************}
