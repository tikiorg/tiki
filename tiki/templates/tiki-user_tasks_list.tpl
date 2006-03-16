{*Smarty template*}


{* start ************ Task list ***************}
<form action="tiki-user_tasks.php" method="post">
<table class="normal">
	<tr>
		<td colspan="6" class="normal">
			<div align="right">
				{tr}Tasks per page{/tr}
				<select name="tasks_maxRecords">
				<option value="-1" {if $tasks_maxRecords eq -1} selected="selected"{/if}>{tr}all{/tr}</option>
				<option value="2" {if $tasks_maxRecords eq 2} selected="selected"{/if}>2</option>
				<option value="5" {if $tasks_maxRecords eq 5} selected="selected"{/if}>5</option>
				<option value="10" {if $tasks_maxRecords eq 10} selected="selected"{/if}>10</option>
				<option value="20" {if $tasks_maxRecords eq 20} selected="selected"{/if}>20</option>
				<option value="30" {if $tasks_maxRecords eq 30} selected="selected"{/if}>30</option>
				<option value="40" {if $tasks_maxRecords eq 40} selected="selected"{/if}>40</option>
				<option value="50" {if $tasks_maxRecords eq 50} selected="selected"{/if}>50</option>
				</select>
			</div>
		</td>
	</tr>
<tr>
<td class="heading" style="text-align:right;" >&nbsp;</td>
<td class="heading" ><a class="tableheading" href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}start{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}end{/tr}</a></td>
<td style="text-align:right;" class="heading" ><a class="tableheading" href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'priority_desc'}priority_asc{else}priority_desc{/if}">{tr}priority{/tr}</a></td>
<td style="text-align:right;" class="heading" ><a class="tableheading" href="tiki-user_tasks.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'percentage_desc'}percentage_asc{else}percentage_desc{/if}">{tr}completed{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=task_i loop=$tasklist}
<tr>
<td class="prio{$tasklist[task_i].priority}">
<input {if $tasklist[task_i].disabled} disabled = "disabled" {/if}  type="checkbox" name="task[{$tasklist[task_i].taskId}]" />
{if $tasklist[task_i].deleted} 
	{if $tasklist[task_i].creator ne $user}
		<img src='img/icons2/delete_all.gif' width='16' height='16' border='0' alt='{tr}delete{/tr}' />
	{else}
		<img src='img/icons/trash.gif' width='16' height='16' border='0' alt='{tr}trash{/tr}' />
	{/if}
{/if}
{if (($tasklist[task_i].creator eq $tasklist[task_i].user) and ($tasklist[task_i].user eq $user)) }
	{*private task*}
{else}
	{if ($tasklist[task_i].user eq $user) }
		{*recived task*}
		&gt;&gt;
		{if (($tasklist[task_i].accepted_creator eq 'n') or ($tasklist[task_i].accepted_user eq 'n')) }
			<img src="{$img_not_accepted}" height="{$img_not_accepted_height}" width="{$img_not_accepted_width}" title='{tr}not accepted by one user{/tr}'  border='0' alt='{tr}not accepted user{/tr}' />
		{else}
			{if ($tasklist[task_i].accepted_user eq '')}
				<img src="{$img_me_waiting}" height="{$img_me_waiting_height}" width="{$img_me_waiting_width}" title='{tr}waiting for me{/tr}' border='0' alt='{tr}waiting for me{/tr}' />
			{else}
				{if ($tasklist[task_i].accepted_creator eq 'y')} 
					<img src="{$img_accepted}" height="{$img_accepted_height}" width="{$img_accepted_width}" title='{tr}accepted by task user and creator{/tr}' border='0' alt='{tr}accepted user and creator{/tr}' />
				{else}
					<img src="{$img_he_waiting}"  height="{$img_he_waiting_height}" width="{$img_he_waiting_width}" border='0' alt='{tr}waiting for other user{/tr}' title='{tr}waiting for other user{/tr}' />
				{/if}
			{/if}
		{/if}
	{elseif ($tasklist[task_i].creator eq $user) }
		{*submitted task*}
		&lt;&lt;
		{if (($tasklist[task_i].accepted_creator eq 'n') or ($tasklist[task_i].accepted_user eq 'n')) }
			<img src="{$img_not_accepted}" height="{$img_not_accepted_height}" width="{$img_not_accepted_width}" title='{tr}not accepted by one user{/tr}'  border='0' alt='{tr}not accepted user{/tr}' />
		{else}
			{if ($tasklist[task_i].accepted_user eq '')}
				{if ($tasklist[task_i].accepted_creator eq 'y')}
					 <img src="{$img_he_waiting}"  height="{$img_he_waiting_height}" width="{$img_he_waiting_width}" border='0' alt='{tr}waiting for other user{/tr}' title='{tr}waiting for other user{/tr}' />
				{else}
					<img src="{$img_me_waiting}" height="{$img_me_waiting_height}" width="{$img_me_waiting_width}" title='{tr}waiting for me{/tr}' border='0' alt='{tr}waiting for me{/tr}' />
				{/if}
			{else}
				{if ($tasklist[task_i].accepted_creator eq 'y')}
					<img src="{$img_accepted}" height="{$img_accepted_height}" width="{$img_accepted_width}" title='{tr}accepted by task user and creator{/tr}' border='0' alt='{tr}accepted user and creator{/tr}' />
				{else}
					<img src="{$img_me_waiting}" height="{$img_me_waiting_height}" width="{$img_me_waiting_width}" title='{tr}waiting for me{/tr}' border='0' alt='{tr}waiting for me{/tr}' />
				{/if}
			{/if}
		{/if}
	{else}
		{*shared task*}
		&gt;&lt;
	{/if}
{/if}
	</td>
	<td class="prio{$tasklist[task_i].priority}">
<a {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="link" href="tiki-user_tasks.php?taskId={$tasklist[task_i].taskId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;tiki_view_mode=view&amp;find={$find}">{$tasklist[task_i].title}</a></td>
<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}"><div class="mini">{$tasklist[task_i].start|date_format:"{tr}%m/%d/%Y [%H:%M]{/tr}"}</div></td>
<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}"><div class="mini">{$tasklist[task_i].end|date_format:"{tr}%m/%d/%Y [%H:%M]{/tr}"}</div></td>
<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">{$tasklist[task_i].priority}</td>
<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">
<select {if $tasklist[task_i].disabled} disabled = "disabled" {/if}  name="task_perc[{$tasklist[task_i].taskId}]">
	<option value="w" {if $tasklist[task_i].percentage_null } selected = "selected"  {/if}>{tr}waiting{/tr}</option>	
	{section name=zz loop=$percs}
		<option value="{$percs[zz]|escape}" {if $tasklist[task_i].percentage eq $percs[zz] and !$tasklist[task_i].percentage_null} selected = "selected" {/if} >{$percs[zz]}%</option>	
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
	<td class="formcolor" colspan="3" style="text-align:left;">
		<img src="img/icons2/arrow.gif" width="16" height="24" style="border:0" alt="arrow" />
        <select name="action">
          <option value="" >{tr}select one{/tr}</option>
          <option value="waiting_marked" >{tr}waiting{/tr}</option>
          <option value="open_marked" >{tr}open{/tr}</option>
          <option value="complete_marked" >{tr}completed{/tr}</option>
          <option value="move_marked_to_trash">{tr}trash{/tr}</option>
          <option value="remove_marked_from_trash">{tr}undo trash{/tr}</option>
        </select>
		<input type="submit" name="update_tasks" value="{tr}go{/tr}" />
	</td>
		<td class="formcolor" colspan="3" style="text-align:right;">
		<input type="submit" name="update_percentage" value="{tr}go{/tr}" />
		<img src="img/icons2/arrow_fliped.gif" width="16" height="24" style="border:0" alt="arrow" />
	</td>
</tr>
<tr>
	<td class="formcolor" colspan="6" style="text-align:center;">
		&nbsp;&nbsp;{tr}show:{/tr}
		&nbsp;<input  name="show_private" {if $show_private} checked="checked" {/if} type="checkbox" />{tr}private{/tr}
		&nbsp;<input  name="show_received" {if $show_received} checked="checked" {/if} type="checkbox" />&gt;&gt; {tr}received{/tr}
		&nbsp;<input  name="show_submitted" {if $show_submitted} checked="checked" {/if} type="checkbox" />&lt;&lt; {tr}submitted{/tr}
		&nbsp;<input  name="show_shared" {if $show_shared} checked="checked" {/if} type="checkbox" />&gt;&lt; {tr}shared{/tr}
		&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;<input  name="show_trash" {if $show_trash} checked="checked" {/if} type="checkbox" />{tr}trash{/tr}
		&nbsp;<input  name="show_completed" {if $show_completed} checked="checked" {/if} type="checkbox" />{tr}completed{/tr}
		{if ($admin_mode)}
		&nbsp;&nbsp;
		<a class="highlight" >
		<input name="show_admin" {if $show_admin} checked="checked" {/if} type="checkbox" />{tr}all shared tasks{/tr}</a>
		{/if}
	</td>
</tr>
<tr>
	<td class="formcolor" colspan="6" style="text-align:center;">
		<input type="submit" name="reload" value="{tr}reload{/tr}" />
	</td>
</tr>

</table>
</form>
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-user_tasks.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-user_tasks.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-user_tasks.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}<br />
{$cant} {tr}Tasks{/tr}
</div>
<br />
<br />


{* end ************ Task list ***************}


