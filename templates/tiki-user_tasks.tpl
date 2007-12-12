{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_tasks.php?">{tr}Tasks{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_tasks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Tasks tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
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
  <table class="findtable">
    <tr>
      <td class="findtable">{tr}Find{/tr}</td>
      <td class="findtable">
        <div align="left">
	  <form method="get" action="tiki-user_tasks.php">
	    <input type="text" name="find" value="{$find|escape}" />
            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
            <input type="submit" value="{tr}Find{/tr}" name="search" />
	  </form>
        </div>
      </td>
    </tr>
  </table>
{/if}

<div class="wiki-edithelp"  id='edithelpzone' >
<table width="100%">
{if $tiki_p_tasks_receive eq 'y'}
<tr>
	<td>
		<img src="pics/icons/task_received.png" title="{tr}Received{/tr}" width="16" height="16" align="middle" border="0" alt="{tr}Received{/tr}" /> 
	</td>
	<td>
		{tr}Received task{/tr}. {tr}You received this task, please read and execute it{/tr}.
	</td>
</tr>
{/if}
{if $tiki_p_tasks_send eq 'y'}
<tr>
	<td>
		<img src="pics/icons/task_submitted.png" title="{tr}Submitted{/tr}" width="16" height="16" align="middle" border="0" alt="{tr}Submitted{/tr}" /> 
	</td>
	<td>
		{tr}Send task{/tr}. {tr}You send this task to a other user{/tr}.
	</td>
</tr>
{/if}
{if $tiki_p_tasks_receive eq 'y' or $tiki_p_tasks_send eq 'y'}
<tr>
	<td>
		<img src="pics/icons/task_shared.png" title="{tr}Shared{/tr}" width="16" height="16" align="middle" border="0" alt="{tr}Shared{/tr}" /> 
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
