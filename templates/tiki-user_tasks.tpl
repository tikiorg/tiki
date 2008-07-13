{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_tasks.php?">{tr}Tasks{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">
{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_tasks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Tasks tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>
{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
<br /><br />
{/if}
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

{if (not $show_form)} 
  <span class="button2"><a href="tiki-user_tasks.php?emty_trash=Empty trash" class="linkbut">{tr}Empty Trash{/tr}</a></span>
{/if}

<br /><br />
{* start ************ Search  ***************}
{if (not $show_form)} 
  {include file='find.tpl' _sort_mode='y'}
{/if}

<div class="wiki-edithelp"  id='edithelpzone' >
<table width="100%">
{if $tiki_p_tasks_receive eq 'y'}
<tr>
	<td>
		{icon _id='task_received' align="middle"} 
	</td>
	<td>
		{tr}Received task{/tr}. {tr}You received this task, please read and execute it{/tr}.
	</td>
</tr>
{/if}
{if $tiki_p_tasks_send eq 'y'}
<tr>
	<td>
		{icon _id='task_submitted' align="middle"} 
	</td>
	<td>
		{tr}Send task{/tr}. {tr}You send this task to a other user{/tr}.
	</td>
</tr>
{/if}
{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}
<tr>
	<td>
		{icon _id='task_shared' align="middle"} 
	</td>
	<td>
		{tr}Shared task{/tr}. {tr}This task is public to a special group{/tr}.
	</td>
</tr>
{/if}
<tr>
	<td>
		<img src="{$img_accepted}" height="{$img_accepted_height}" width="{$img_accepted_width}" title='{tr}Accepted by Task User and Creator{/tr}' border='0' alt='{tr}Accepted User and Creator{/tr}' />:
	</td>
	<td>
		{tr}Task is accepted by user and creator{/tr}.
	</td>
</tr>
<tr>
	<td>
		<img src="{$img_not_accepted}" height="{$img_not_accepted_height}" width="{$img_not_accepted_width}" title='{tr}Not Accepted by One User{/tr}'  border='0' alt='{tr}Not Accepted User{/tr}' />:
	</td>
	<td>
		{tr}Task is rejected by one user{/tr}.
	</td>
</tr>
{if $tiki_p_tasks_receive eq 'y'}
<tr>
	<td>
		<img src="{$img_me_waiting}"  height="{$img_me_waiting_height}" width="{$img_me_waiting_width}" border='0' alt="{tr}Waiting for Me{/tr}" title="{tr}Waiting for Me{/tr}" />
	</td>
	<td>
		{tr}Task is not accepted by you, read the task and accept or reject it{/tr}.
	</td>
</tr>
{/if}
{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}
<tr>
	<td>
		<img src="{$img_he_waiting}"  height="{$img_he_waiting_height}" width="{$img_he_waiting_width}" border='0' alt="{tr}Waiting for Other User{/tr}" title="{tr}Waiting for Other User{/tr}" />
	</td>
	<td>
		{tr}Task is not accepted/rejected by other user{/tr}.
	</td>
</tr>
{/if}
</table>
</div>
<br />
{if $admin_mode}<div align="center"><a class="highlight" >{tr}Admin Mode{/tr}</a></div><br />{/if}
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


<br />
<br />
{* start ************ Search ***************}
