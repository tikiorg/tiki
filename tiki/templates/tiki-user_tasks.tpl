{*Smarty template*}
<a class="pagetitle" href="tiki-user_tasks.php?">{tr}Tasks{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_tasks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Tasks tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}






{include file=tiki-mytiki_bar.tpl}
<br /><br />

[
<a class="link" href="tiki-user_tasks.php?show_form=y">{tr}New task{/tr}</a>
&nbsp;|&nbsp;
<a class="link" href="tiki-user_tasks.php">{tr}Task list{/tr}</a>
]
<br /><br />
{* start ************ Edit Form ***************}

{if ($show_form)} 
<form action="tiki-user_tasks.php" method="post">


<input type="hidden" name="taskId" value="{$taskId|escape}" />
<input type="hidden" name="date_Day" value="{$date_Day|escape}" />
<input type="hidden" name="date_Month" value="{$date_Month|escape}" />
<input type="hidden" name="date_Year" value="{$date_Year|escape}" />
<input type="hidden" name="date_Hour" value="{$date_Hour|escape}" />
<input type="hidden" name="date_Minute" value="{$date_Minute|escape}" />
<table class="normal">
<colgroup><col width="25%" span="4" /></colgroup>

  <tr>
  	{if ($saved)} 
	<td colspan="4" class="highlight"><div align="center"><h3>{tr}Task saved{/tr}</h3></div></td>
	{else}
  	<td colspan="4" class="formcolor">
	<div align="center">
		<h3>
		{if ($info.belongs_to eq 0)} {tr}Open a new task{/tr}
		{else}
		{tr}Edit a task{/tr}
		{/if}
		</h3>
	</div>
	</td>
  {/if}
  </tr>
  <tr>
	<td class="formcolor">{tr}Created by{/tr}</td> 
	<td class="formcolor" colspan="2" >
		<b>{$info.creator|escape}</b>
		&nbsp;&nbsp; <b>{$info.date|date_format:"%d/%m/%Y -- %H:%M"}</b>
		&nbsp;&nbsp; version: <b>{$info.task_version|escape}</b>
		&nbsp;&nbsp; newest version: <b>{$info.newest_version|escape}</b>
	</td>
	<td class="formcolor" colspan="2" >
		<div align="right">taskId: 
		<b>{$info.taskId|escape}</b>
		&nbsp;belongs to: <b>{$info.belongs_to|escape}</b>
		&nbsp;&nbsp; right: <b>{$right|escape}</b>
		</div>
	</td>
	</tr>
  <tr>
  	<td class="formcolor">{tr}Task user{/tr}</td> 
  		<td colspan="3" class="formcolor">
			{if ($receive_users)} 
				<select {if (!$editable.user) } disabled = "disabled" {/if} name="task_user">
				{section name=user_i loop=$receive_users} 
					<option value="{$receive_users[user_i].login}" 
						{if ( $receive_users[user_i].login eq $info.user) } selected="selected" {/if}>
							{$receive_users[user_i].login}
					</option>
				{/section}
				</select>
			{else}
			<input {if (!$editable.user) } disabled="disabled"{/if} type="text" name="task_user" value="{$info.user|escape}" />
			{/if}
			{if ($right ne 'private')} 
				&nbsp;
				<input  {if (!$editable.rights_by_creator) } disabled="disabled" {/if} {if $info.rights_by_creator eq 'y'} checked="checked"{/if}  name="rights_by_creator" type="checkbox" />
				&nbsp;{tr}Only the creator can delete this task{/tr} 
			 {/if}
		</td>
  </tr>
	<tr>
		<td class="formcolor">{tr}Title{/tr}</td>
		<td colspan="3" class="formcolor">
			<input  style="width:98%;" {if (!$editable.title) } disabled="disabled"{/if} type="text" name="title" value="{$info.title|escape}" />
	</td>
  <tr><td class="formcolor">{tr}Description{/tr}</td>
      <td colspan="3" class="formcolor">
        <textarea {if (!$editable.description) } disabled="disabled"{/if} style="width:98%;"  rows="10" cols="80" name="description">{$info.description|escape}</textarea>
      </td>
  </tr>
	<tr><td  class="formcolor">{tr}Start{/tr}</td>
		<td colspan="3" class="formcolor">
			{if (!$editable.start) } 
			<b>{$info.start|date_format:"%d/%m/%Y -- %H:%M"}</b>
			{else}
			{html_select_date time=$start_date prefix="start_" end_year="+4" field_order=DMY}
			&nbsp;-&nbsp;
			{html_select_time minute_interval=10 time=$start_date prefix="start_" display_seconds=false use_24_hours=true}
			{/if}
			&nbsp;<input name="use_start_date" {if (!$editable.start) } disabled = "disabled" {/if} {if $info.start or $right eq  'new'} checked="checked" {/if} type="checkbox" />
			&nbsp;use start date and time
		</td>
	</tr>
	<tr><td  class="formcolor">{tr}End{/tr}</td>
		<td colspan="3" class="formcolor">
			{if (!$editable.end) } 
			<b>{$info.end|date_format:"%d/%m/%Y -- %H:%M"}</b>
			{else}
			{html_select_date time=$end_date prefix="end_" end_year="+4" field_order=DMY"}
			&nbsp;-&nbsp;
			{html_select_time minute_interval=10 time=$end_date prefix="end_" display_seconds=false use_24_hours=true}
			{/if}
			&nbsp;<input name= "use_end_date" {if (!$editable.end) } disabled = "disabled" {/if} {if $info.end} checked="checked" {/if} type="checkbox" />
			&nbsp;use end date and time
		</td>
	</tr>
	<tr><td class="formcolor">{tr}Status{/tr}</td>
		<td colspan="3" class="formcolor">
		<select {if (!$editable.status) } disabled = "disabled" {/if} name="status">
			<option value="w" selected="selected">{tr}waiting / not started{/tr}</option>
			<option value="o" {if $info.status eq 'o'} selected="selected"{/if}>{tr}open / in process{/tr}</option>
			<option value="c" {if $info.status eq 'c'} selected="selected"{/if}>{tr}completed (100%){/tr}</option>
		</select>
		&nbsp;&nbsp;
		<b>{$info.completed|date_format:"%d/%m/%Y -- %H:%M"}</b>
		</td>
	</tr>  
	
	<tr>
		<td class="formcolor">{tr}Priority{/tr}</td>
		<td colspan="3"  class="formcolor">
		<select {if (!$editable.priority) } disabled = "disabled" {/if} name="priority"  >
			<option value="1" {if $info.priority eq 1} selected="selected"{/if}>1</option>
			<option value="2" {if $info.priority eq 2} selected="selected"{/if}>2</option>
			<option value="3" {if $info.priority eq 3} selected="selected"{/if}>3</option>
			<option value="4" {if $info.priority eq 4} selected="selected"{/if}>4</option>
			<option value="5" {if $info.priority eq 5} selected="selected"{/if}>5</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Percentage completed{/tr}</td>
		<td colspan="3"  class="formcolor">  
			 <select name="percentage" {if (!$editable.percentage) } disabled = "disabled" {/if}>
				{section name=zz loop=$percs}
					<option value="{$percs[zz]|escape}" {if $info.percentage eq $percs[zz]} selected = "selected" {/if} > {$percs[zz]}% </option>	
				{/section}
      		</select>
		</td>
	</tr>
	<tr><td  class="formcolor">{tr}shared for group{/tr}</td>
		<td colspan="3" class="formcolor">
		<select {if (!$editable.public_for_group) } disabled = "disabled" {/if} name="public_for_group">
			<option></option>
		{section name=groups_i loop=$receive_groups} 
			<option value="{$receive_groups[groups_i].groupName}" 
				{if ( $receive_groups[groups_i].groupName eq $info.public_for_group) } selected="selected" {/if}>
					{$receive_groups[groups_i].groupName}
			</option>
		{/section}
		</select>
		</td>
	</tr> 
	{if (($right ne 'private') and ($right ne 'new') and ($info.user != $info.creator))}
		<tr><td class="formcolor">{tr}accepted by user{/tr}</td>
		<td class="formcolor">
			{if $info.accepted_user eq 'y'} {tr}yes{/tr}
			{else} {if $info.accepted_user eq 'n'} {tr}no / rejected{/tr}
			{else} {tr}waiting{/tr}{/if}{/if}
		</select>
		</td>
		<td class="formcolor">{tr}accepted by creator{/tr}</td>
		<td class="formcolor">
			{if $info.accepted_creator eq 'y'} {tr}yes{/tr}
			{else} {if $info.accepted_creator eq 'n'} {tr}no / rejected{/tr}
			{else} {tr}waiting{/tr}{/if}{/if}
		</td>
	</tr> 
	{/if} 
	<tr>
		<td class="formcolor">{tr}Info{/tr}</td>
		<td colspan="3" class="formcolor">
			{tr}This message will be send to users if you are makeing changes of assigned tasks{/tr}<br/>
			<textarea {if (!$editable.info) } disabled = "disabled" {/if}  style="width:98%;" rows="2" cols="80" name="info">{$info.info|escape}</textarea>
		</td>
	</tr>
	{if $info.task_version > 0}
	<tr>
		<td class="formcolor">{tr}Modified by{/tr}</td></td>
		<td colspan="3" class="formcolor">
			<b>{$info.lasteditor}</b>
			&nbsp;&nbsp;<b>{$info.changes|date_format:"%d/%m/%Y -- %H:%M"}</b>
		</td>
	</tr>  
	{/if}
	
	{if $info.deleted}
	<tr><td class="formcolor">{tr}Maked as deleted{/tr}</td>
		<td colspan="3" class="formcolor"><b>{$info.deleted|date_format:"%d/%m/%Y -- %H:%M"}</b></td>
	</tr> 
	{/if}
	<tr>
	{if $right eq 'view'}
	<tr>
		<td class="formcolor">
		{if $info.deleted and ($info.user eq $user or $info.creator eq $user)}
				<input type="submit" name="remove_task_from_trash" value="{tr}remove from trash{/tr}" />
		{/if}
		</td>
		<td colspan="3" class="formcolor"><div align="center"><b>{tr}your having onl view rights{/tr}</b></div></td>
	</tr> 
	{else}
		<td class="formcolor" colspan="2" >
			<div align="left">
				{if (($right ne 'private') and ($right ne 'new')) }
					<input type="submit" name="accept" value="{tr}accept{/tr}" />
					<input type="submit" name="reject" value="{tr}reject{/tr}" />
				{/if}
				<input type="submit" name="move_task_into_trash" value="{tr}move to trash{/tr}" />
			</div>
		</td>
		<td class="formcolor" colspan="2" >
			<div align="center">
				<input type="submit" name="save" value="{tr}save{/tr}" />
				<input type="submit" name="reload" value="{tr}reload{/tr}" />
			</div>
		</td>
	{/if}
  </tr>
</table>
</form>

{if ($history) }
<table class="normal">
  <tr>
    <td colspan="2" class="formcolor"><div align="center"><h1>{tr}Histroy{/tr}</h1></div></td>
  </tr>
  <tr><td colspan="2" class="formcolor"><div align="center">
		<form action="tiki-user_tasks.php" method="post">
			{tr}show version{/tr}
			&nbsp;
			<select name="taskId">
			{section name=histroy_i loop=$history} 
				<option value="{$history[histroy_i].taskId}" 
					{if ( $history[histroy_i].task_version eq $info.task_version ) } selected="selected" {/if}>
						{$history[histroy_i].task_version}
				</option>
			{/section}
			</select>
			<input type="submit" name="view_histroy" value="{tr}view{/tr}" />
		</form>
    </td>
  </tr>
</table>
<br />
<br />


{/if}
{/if}

{* end ************ Edit Form ***************}





{* start ************ Task list ***************}
<form action="tiki-user_tasks.php" method="post">
<table class="normal">
	<tr>
		<td colspan="6" class="normal">
			<div align="right">
				{tr}Tasks per page{/tr}
				<select name="tasks_maxRecords">
				<option value="-1" {if $tasks_maxRecords eq -1} selected="selected"{/if}>all</option>
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
<td class="heading">&nbsp;</td>
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
		<img src="img/icons2/delete_all.gif" width="16" height="16" border="0">
	{else}
		<img src="img/icons/trash.gif" width="16" height="16" border="0">
	{/if}
{/if}
</td>
<td class="prio{$tasklist[task_i].priority}"><a {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="link" href="tiki-user_tasks.php?taskId={$tasklist[task_i].taskId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{$tasklist[task_i].title}</a></td>
<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}">{$tasklist[task_i].start|date_format:"%d/%m/%Y-%H:%M"}</td>
<td {if $tasklist[task_i].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$tasklist[task_i].priority}">{$tasklist[task_i].end|date_format:"%d/%m/%Y-%H:%M"}</td>
<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">{$tasklist[task_i].priority}</td>
<td style="text-align:right;{if $tasklist[task_i].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$tasklist[task_i].priority}">
<select {if $tasklist[task_i].disabled} disabled = "disabled" {/if}  name="task_perc[{$tasklist[task_i].taskId}]">
	{section name=zz loop=$percs}
		<option value="{$percs[zz]|escape}" {if $tasklist[task_i].percentage eq $percs[zz]} selected = "selected" {/if} >{$percs[zz]}%</option>	
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
	<td class="formcolor" colspan="6" style="text-align:left;">
		<img src="img/icons2/arrow.gif" width="16" height="24" border="0">
        <select name="action">
          <option value="update_percentage" selected="selected">{tr}update all percents{/tr}</option>
          <option value="move_marked_to_trash">{tr}move marked to trash{/tr}</option>
          <option value="remove_marked_from_trash">{tr}remove marked to trash{/tr}</option>
          <option value="complete_marked" >{tr}complete marked{/tr}</option>
        </select>
		&nbsp;&nbsp;{tr}show:{/tr}
		&nbsp;<input  name="show_private" {if $show_private} checked="checked" {/if} type="checkbox" />{tr}private{/tr}
		&nbsp;<input  name="show_received" {if $show_received} checked="checked" {/if} type="checkbox" />{tr}received{/tr}
		&nbsp;<input  name="show_submitted" {if $show_submitted} checked="checked" {/if} type="checkbox" />{tr}submitted{/tr}
		&nbsp;<input  name="show_shared" {if $show_shared} checked="checked" {/if} type="checkbox" />{tr}shared{/tr}
		{if ($task_admin)}
		&nbsp;<input  name="show_admin" {if $show_admin} checked="checked" {/if} type="checkbox" />{tr}admin{/tr}
		{/if}
		&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;<input  name="show_trash" {if $show_trash} checked="checked" {/if} type="checkbox" />{tr}trash{/tr}
		&nbsp;<input  name="show_completed" {if $show_completed} checked="checked" {/if} type="checkbox" />{tr}completed{/tr}
	</td>
</tr>
<tr>
	<td class="formcolor" colspan="6" style="text-align:center;">
		<input type="submit" name="update" value="{tr}update{/tr}" />
	</td>
</tr>

</table>
</form>
<div class="mini">
<div align="center">
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
{/if}<br/>
{$cant} {tr}Tasks{/tr}
</div>
</div>
<br />
<br />


{* end ************ Task list ***************}


{* start ************ Serach  ***************}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <div align="left">
	<form method="get" action="tiki-user_tasks.php">
		<input type="text" name="find" value="{$find|escape}" />
		<input type="submit" value="{tr}find{/tr}" name="search" />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	</form>
   </div>
   </td>
   <td class="findtable">
	<div align="right">
		<form method="get" action="tiki-user_tasks.php">
			<img src="img/icons/trash.gif" title="trash" width="16" height="16" align="middle" border="0">
			&nbsp;
			<input type="submit" value="{tr}emty trash{/tr}" name="emty_trash" />
		</form>
	</div>
   </td>
</tr>
</table>
<br />
<br />
{* start ************ Serach ***************}
