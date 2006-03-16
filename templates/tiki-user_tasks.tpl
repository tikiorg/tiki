{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_tasks.php?">{tr}Tasks{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_tasks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Tasks tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}' /></a>
{/if}</h1>




{include file=tiki-mytiki_bar.tpl}
<br /><br />

<span class="button2"><a class="linkbut" href="tiki-user_tasks.php?show_form=y">{tr}New task{/tr}</a>
</span>
<span class="button2"><a class="linkbut" href="tiki-user_tasks.php">{tr}Task list{/tr}</a>
</span>
{ if $task_admin }
{if $admin_mode}
<span class="button2"><a class="linkbut" href="tiki-user_tasks.php?taskId={$taskId}&amp;admin_mode_off=on">{tr}Admin mode off{/tr}</a>
</span>
{else}
<span class="button2"><a class="linkbut" href="tiki-user_tasks.php?taskId={$taskId}&amp;admin_mode=on">{tr}Admin mode on{/tr}</a>
</span>
{/if}
{/if}
<span class="button2"><a href="#" onclick="javascript:flip('edithelpzone'); return false;" class="linkbut">{tr}Task help{/tr}</a></span>

<div class="wiki-edithelp"  id='edithelpzone' >
<table width="100%">
<tr>
	<td>
		<strong>&gt;&gt;:</strong> 
	</td>
	<td>
		{tr}Received task{/tr}. {tr}You received this task, please read and execute it{/tr}.
	</td>
</tr>
<tr>
	<td>
		<strong>&lt;&lt;:</strong> 
	</td>
	<td>
		{tr}Send task{/tr}. {tr}You send this task to a other user{/tr}.
	</td>
</tr>
<tr>
	<td>
		<strong>&gt;&lt;:</strong> 
	</td>
	<td>
		{tr}Shared task{/tr}. {tr}This task is public to a special group{/tr}.
	</td>
</tr>
<tr>
	<td>
		<img src="{$img_accepted}" height="{$img_accepted_height}" width="{$img_accepted_width}" title='{tr}accepted by task user and creator{/tr}' border='0' alt='{tr}accepted user and creator{/tr}' />:
	</td>
	<td>
		{tr}Task is accepted by user and creator{/tr}.
	</td>
</tr>
<tr>
	<td>
		<img src="{$img_not_accepted}" height="{$img_not_accepted_height}" width="{$img_not_accepted_width}" title='{tr}not accepted by one user{/tr}'  border='0' alt='{tr}not accepted user{/tr}' />:
	</td>
	<td>
		{tr}Task is rejected by one user{/tr}.
	</td>
</tr>
<tr>
	<td>
		<img src="{$img_me_waiting}"  height="{$img_me_waiting_height}" width="{$img_me_waiting_width}" border='0' alt="{tr}waiting for me{/tr}" title="{tr}waiting for me{/tr}"}
	</td>
	<td>
		{tr}Task is not accepted by you, read the task and accept or reject it{/tr}.
	</td>
</tr>
<tr>
	<td>
		<img src="{$img_he_waiting}"  height="{$img_he_waiting_height}" width="{$img_he_waiting_width}" border='0' alt="{tr}waiting for other user{/tr}" title="{tr}waiting for other user{/tr}" />
	</td>
	<td>
		{tr}Task is not accepted/rejected by other user{/tr}.
	</td>
</tr>
</table>
</div>
<br /><br />
{if $admin_mode}<div align="center"><a class="highlight" >{tr}admin mode{/tr}</a></div><br />{/if}
{* start ************ view  ***************}
{if (($tiki_view_mode eq 'view') or ($tiki_view_mode eq 'preview'))}
{include file=tiki-user_tasks_view.tpl}
{/if}

{* end ************ View task ***************}

{* start ************ Edit Form ***************}

{if ($show_form)} 
{include file=tiki-user_tasks_edit.tpl}
{/if}

{* end ************ Edit Form ***************}





{* start ************ Task list ***************}
{if ( not $show_form )} 
{include file=tiki-user_tasks_list.tpl}
{/if} 

{* end ************ Task list ***************}


{* start ************ Serach  ***************}
{if (not $show_form)} 
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
			<img src="img/icons/trash.gif" title="{tr}trash{/tr}" width="16" height="16" align="middle" border="0" alt="{tr}trash{/tr}" /> 
			&nbsp;
			<input type="submit" value="{tr}empty trash{/tr}" name="emty_trash" />
		</form>
	</div>
   </td>
</tr>
</table>
{/if}
<br />
<br />
{* start ************ Serach ***************}
