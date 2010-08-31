<form action="tiki-user_tasks.php" method="post">

<input type="hidden" name="taskId" value="{$taskId|escape}" />
<input type="hidden" name="task_version" value="{$info.task_version|escape}" />
<input type="hidden" name="last_version" value="{$info.last_version|escape}" />
<input type="hidden" name="date_Day" value="{$date_Day|escape}" />
<input type="hidden" name="date_Month" value="{$date_Month|escape}" />
<input type="hidden" name="date_Year" value="{$date_Year|escape}" />
<input type="hidden" name="date_Hour" value="{$date_Hour|escape}" />
<input type="hidden" name="date_Minute" value="{$date_Minute|escape}" />
<input type="hidden" name="creator" value="{$info.creator|escape}" />
{if ($tiki_view_mode eq 'view')}
 <span class="tabbut">
    <a href="tiki-user_tasks.php?taskId={$taskId}&amp;tiki_view_mode=edit" class="tablink">{tr}Edit Task{/tr}</a>
    </span>

{/if}

{if ($info.creator eq $user or $info.user eq $user or $admin_mode) and ($info.task_version eq $info.last_version)}
{ if ($info.taskId > 0 and $info.creator ne $info.user) }
    <span class="tabbut">
    {html_image file='img/icons2/tick.gif' width='15' height='15' alt="{tr}Accept{/tr}"}
    <a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;task_accept=on" class="tablink">{tr}Accept{/tr}</a>
    </span>
    <span class="tabbut">
    {html_image file='img/icons2/error.gif' width='14' height='14' alt="{tr}Red{/tr}"}
    <a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;task_not_accept=on" class="tablink">{tr}NOT accept{/tr}</a>
    </span>
{/if}
{ if $info.deleted }
    <span class="tabbut">
    <a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;remove_from_trash=on" class="tablink">{tr}Remove from Trash{/tr}</a>
    </span>
{else}
    <span class="tabbut">
	{html_image file='img/icons/trash.gif' width='16' height='16' alt="{tr}Trash{/tr}"}
    <a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;move_into_trash=on" class="tablink">{tr}Move into Trash{/tr}</a>
    </span>
{/if}
{/if}
<table class="formcolor">
<colgroup><col width="25%" span="4" /></colgroup>
  <tr>
  	{if ($saved)} 
	<td colspan="4" class="highlight"><div align="center"><h3>{tr}Task saved{/tr}</h3></div></td>
	{else}
  	<td colspan="4">
	<div align="center">
		<h3>
		{if ($info.taskId > 0)}
			{if (($info.creator eq $user) or ($info.user eq $user) or $admin_mode) and ($info.task_version eq $info.last_version)}
				{tr}Edit Task{/tr} 
			{else}
				{tr}View Task{/tr} 
			{/if}
		{else}
			{tr}Open a new task{/tr}
		{/if}
		</h3>
	</div>
	</td>
  {/if}
  </tr>
  <tr>
	<td>{tr}Created by{/tr}</td> 
	<td colspan="2">
		<b>{$info.creator|escape}</b>
		&nbsp;&nbsp; <b>{$info.created|date_format:"%d/%m/%Y -- %H:%M"}</b>
		&nbsp;&nbsp; 
		{if ($info.task_version > 0) } 
		<a class="link" href="tiki-user_tasks.php?taskId={$taskId}&amp;show_history={$info.task_version-1}">{icon _id='resultset_previous' align="middle"}</a>
  		{/if}
		{tr}Version{/tr}: <b>{$info.task_version+1}</b>
		{if $info.task_version < $info.last_version } 
		<a class="link" href="tiki-user_tasks.php?taskId={$taskId}&amp;show_history={$info.task_version+1}">{icon _id='resultset_next' align="middle"}</a>
  		{/if}
		
		&nbsp;&nbsp; 
		
		<a class="link" href="tiki-user_tasks.php?taskId={$taskId}&amp;show_history={$info.last_version}">{tr}Last Version{/tr}: {$info.last_version+1}</a>
	</td>
	<td colspan="2">
		<div align="right">{tr}taskId{/tr}:
		<b>{$info.taskId|escape}</b>
		</div>
	</td>
	</tr>
  <tr>
  	<td>{tr}Task user{/tr}</td> 
  		<td colspan="3">
			{if ($receive_users)} 
				<select name="task_user" {if ($info.taskId > 0 and !$admin_mode) } disabled="disabled"> <option value="{$info.user}">{$info.user}</option></select><input type="hidden" name="task_user" value="{$info.user}" />{else}>
				{section name=user_i loop=$receive_users} 
					<option value="{$receive_users[user_i].login|escape}" 
						{if ( $receive_users[user_i].login eq $info.user) } selected="selected" {/if}>
							{$receive_users[user_i].login|escape}
					</option>
				{/section}
				</select>
				{/if}
			{else}
			<input type="text" name="task_user" value="{$info.user|escape}" />
			{/if}
			{if (($info.user ne $info.creator) or ($taskId eq 0)) } 
				&nbsp;
				<input {if $info.creator ne $user} disabled="disabled" {/if} 
				{if $info.rights_by_creator eq 'y'} checked="checked" {/if}  name="rights_by_creator" type="checkbox" />
				&nbsp;{tr}Only the creator can delete this task{/tr} 
			 {/if}
		</td>
    </tr>
	<tr>
		<td>{tr}Title{/tr}</td>
		<td colspan="3">
			<input  style="width:98%;" type="text" name="title" value="{$info.title|escape}" />
		</td>
    </tr>
    <tr>
      <td>{tr}Description{/tr}<br /><br />
      </td>
      <td colspan="3">
        {toolbars area_id='edittask'}
        <textarea id='edittask' style="width:98%;" rows="15" cols="80" name="description">{$info.description|escape}</textarea>
      </td>
    </tr>
    <tr><td>{tr}Start{/tr}</td>
		<td colspan="3">
			{html_select_date time=$start_date prefix="start_" end_year="+4" field_order=$prefs.display_field_order}
			&nbsp;-&nbsp;
			{html_select_time minute_interval=10 time=$start_date prefix="start_" display_seconds=false use_24_hours=true}
			&nbsp;<input name="use_start_date" {if $info.start or $taskId eq  0} checked="checked" {/if} type="checkbox" />
			&nbsp;{tr}Use start date and time{/tr}
		</td>
	</tr>
	<tr><td>{tr}End{/tr}</td>
		<td colspan="3">
			{html_select_date time=$end_date prefix="end_" end_year="+4" field_order=$prefs.display_field_order}
			&nbsp;-&nbsp;
			{html_select_time minute_interval=10 time=$end_date prefix="end_" display_seconds=false use_24_hours=true}
			&nbsp;<input name="use_end_date" {if $info.end} checked="checked" {/if} type="checkbox" />
			&nbsp;{tr}Use end date and time{/tr}
		</td>
	</tr>
	<tr><td>{tr}Status{/tr}</td>
		<td colspan="3">
		{if $info.status eq 'o'}{tr}Open / In Process{/tr}
		{else} {if $info.status eq 'o'}{tr}Open / In Process{/tr}
		{else} {tr}Waiting / Not Started{/tr}
		{/if}
		{/if}
		&nbsp;&nbsp;
		<b>{$info.completed|date_format:"%d/%m/%Y -- %H:%M"}</b>
		</td>
	</tr>  
	
	<tr>
		<td>{tr}Priority{/tr}</td>
		<td colspan="3">
		<select name="priority">
			<option value="1" {if $info.priority eq 1} selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
			<option value="2" {if $info.priority eq 2} selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
			<option value="3" {if $info.priority eq 3} selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
			<option value="4" {if $info.priority eq 4} selected="selected"{/if}>4 -{tr}High{/tr}-</option>
			<option value="5" {if $info.priority eq 5} selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>{tr}Percentage completed{/tr}</td>
		<td colspan="3">  
			 <select name="percentage">
					<option value="w" {if $info.percentage_null } selected = "selected"  {/if}>{tr}Waiting{/tr}</option>	
				{section name=zz loop=$percs}
					<option value="{$percs[zz]|escape}" {if $info.percentage eq $percs[zz] and !$info.percentage_null} selected = "selected" {/if} > {$percs[zz]}% </option>	
				{/section}
      		</select>
		</td>
	</tr>
	<tr><td>{tr}Shared for Group{/tr}</td>
		<td colspan="3">
		<select name="public_for_group">
			<option></option>
		{section name=groups_i loop=$receive_groups} 
			<option value="{$receive_groups[groups_i].groupName|escape}" 
				{if ( $receive_groups[groups_i].groupName eq $info.public_for_group) } selected="selected" {/if}>
					{$receive_groups[groups_i].groupName|escape}
			</option>
		{/section}
		</select>
		</td>
	</tr> 
	{if (($info.taskId > 0) and ($info.user ne $info.creator))}
	<tr><td>{tr}Accepted by User{/tr}</td>
		<td>
			{if $info.accepted_user eq 'y'} {tr}Yes{/tr}
			{else} {if $info.accepted_user eq 'n'} {tr}No / Rejected{/tr}
			{else} {tr}Waiting{/tr}{/if}{/if}
		</td>
		<td>{tr}Accepted by Creator{/tr}</td>
		<td>
			{if $info.accepted_creator eq 'y'} {tr}Yes{/tr}
			{else} {if $info.accepted_creator eq 'n'} {tr}No / Rejected{/tr}
			{else} {tr}Waiting{/tr}{/if}{/if}
		</td>
	</tr> 
	{/if} 
	{if ($info.user ne $info.creator and ($info.task_version eq $info.last_version))}
	<tr>
		<td>{tr}Info{/tr}</td>
		<td colspan="3">
			{tr}This message will be send to users if you are makeing changes of assigned tasks{/tr}<br />
			<textarea style="width:98%;" rows="2" cols="80" name="task_info_message">{$info.info|escape}</textarea>
			<input checked="checked" type="checkbox" name="task_send_changes_message" />{tr}Send message with changes{/tr}
		</td>
	</tr>
	{/if} 
	{if $info.task_version > 0}
	<tr>
		<td>{tr}Modified by{/tr}</td>
		<td colspan="3">
			<b>{$info.lasteditor}</b>
			&nbsp;&nbsp;<b>{$info.changes|date_format:"%d/%m/%Y -- %H:%M"}</b>
		</td>
	</tr>  
	{/if}
	
	{if $info.deleted}
	<tr><td>{tr}Marked as deleted{/tr}</td>
		<td colspan="3"><b>{$info.deleted|date_format:"%d/%m/%Y -- %H:%M"}</b></td>
	</tr> 
	{/if}
	<tr>
	{if (($info.creator eq $user) or ($info.user eq $user) or $admin_mode) and ($info.task_version eq $info.last_version) }
{if $info.taskId eq 0 }
	<td colspan="4">
        <div align="center">
			<input checked="checked" type="checkbox" name="send_email_newtask" />{tr}Inform task user by email{/tr}
        </div>
    </td>
	</tr>
    <tr>
	{/if}
	<td colspan="4">
		<div align="center">
			<input type="submit" name="save" value="{tr}Save{/tr}" />
			<input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
			<input type="submit" name="reload" value="{tr}Reload{/tr}" />
		</div>
	</td>
	{else}
		<td colspan="4">
			<div align="center">
				{tr}You can only view this task{/tr}
			</div>
		</td>
	{/if}
  </tr>
</table>
</form>
