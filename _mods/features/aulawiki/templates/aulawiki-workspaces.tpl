<p class="pagetitle">{tr}Workspaces Administration{/tr}</p>
{include file="aulawiki-module_error.tpl" error=$page_error_msg}
<form name="form1" method="post" action="aulawiki-workspaces.php">
  <input name="id" type="hidden" id="id" value="{$workspace.workspaceId}"> 
  <input name="categoryId" type="hidden" id="categoryId" value="{$workspace.categoryId}"> 
  <input name="viewWS" type="hidden" id="viewWS" value="{$viewWS}"> 
  
  <table class="normal">
    <tr> 
      <td class="formcolor"><label for="code">{tr}Code{/tr}</label></td>
      <td class="formcolor">
      {if $workspace.workspaceId!=""}
      	<input name="code" type="hidden" id="code" value="{$workspace.code}"> 
      	{$workspace.code}
      {else}
	      <input name="code" type="text" id="code" value="{$workspace.code}" size="60" maxlength="100">
	  {/if}
      </td>
    </tr>
     <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor"><input name="name" type="text" id="name" value="{$workspace.name}" size="60" maxlength="100"></td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="desc">{tr}Description{/tr}</label></td>
      <td class="formcolor"><textarea name="desc" id="desc" size="60" cols="60" rows="4">{$workspace.description}</textarea></td>
    </tr>
     <tr> 
      <td class="formcolor" ><label>{tr}Created{/tr}</label></td>
      <td class="formcolor">
		  {$created|date_format:"%B %e, %Y %H:%M"}  
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="startDate">{tr}Start Date{/tr}</label></td>
      <td class="formcolor">
		{if $feature_jscalendar eq 'y'}
		<input type="hidden" name="startDate" value="{$startDate}" id="startDate" />
		<span id="start_date_display" class="daterow">{$startDate|date_format:$daformat}</span>
		<script type="text/javascript">
		{literal}Calendar.setup( { {/literal}
		date        : "{$startDate|date_format:"%B %e, %Y %H:%M"}",      // initial date
		inputField  : "startDate",      // ID of the input field
		ifFormat    : "%s",    // the date format
		displayArea : "start_date_display",       // ID of the span where the date is to be shown
		daFormat    : "{$daformat}",  // format of the displayed date
		showsTime   : true,
		singleClick : true,
		align       : "bR",
		firstDay : {$firstDayofWeek},
		timeFormat : {$timeFormat12_24}
		{literal} } );{/literal}
		</script>
		{else}
		{if $start_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
		<input type="text" name="start_freeform" value="{$start_freeform}">
		<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
		{tr}or{/tr}
		{html_select_date time=$startDate prefix="start_" end_year="+4" field_order=DMY}
		{html_select_time minute_interval=5 time=$startDate prefix="starth_" display_seconds=false use_24_hours=true}
		{/if}
		</td>
    </tr>    
    <tr> 
      <td class="formcolor" ><label for="endDate">{tr}End Date{/tr}</label></td>
      <td class="formcolor">
	
		{if $feature_jscalendar eq 'y'}
		<input type="hidden" name="endDate" value="{$endDate}" id="endDate" />
		<span id="end_date_display" class="daterow">{$endDate|date_format:$daformat}</span>
		<script type="text/javascript">
		{literal}Calendar.setup( { {/literal}
		date        : "{$endDate|date_format:"%B %e, %Y %H:%M"}",      // initial date
		inputField  : "endDate",      // ID of the input field
		ifFormat    : "%s",    // the date format
		displayArea : "end_date_display",       // ID of the span where the date is to be shown
		daFormat    : "{$daformat}",  // format of the displayed date
		showsTime   : true,
		singleClick : true,
		align       : "bR",
		firstDay : {$firstDayofWeek},
		timeFormat : {$timeFormat12_24}
		{literal} } );{/literal}
		</script>
		{else}
		{if $end_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
		<input type="text" name="end_freeform" value="{$end_freeform}">
		<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
		{tr}or{/tr}
		{html_select_date time=$endDate prefix="end_" end_year="+4" field_order=DMY}
		{html_select_time minute_interval=5 time=$endDate prefix="endh_" display_seconds=false use_24_hours=true}
		{/if}
		</td>
    </tr>    
    <tr> 
      <td class="formcolor" ><label for="closed">{tr}Closed{/tr}</label></td>
      <td class="formcolor">
		  <input name="closed" id="closed" type="checkbox" value="y" {if $workspace.closed eq 'y'}checked{/if}>    
      </td>
    </tr>
    
     <tr> 
      <td class="formcolor" ><label for="type">{tr}Type{/tr}</label></td>
      <td class="formcolor">
      
      <select name="type" id="type">
	      {section name=i loop=$typesAll}
	      	<option value="{$typesAll[i].id}" {if $typesAll[i].id==$workspace.type}selected{/if}>{$typesAll[i].name}</option>
	      {/section}
      </select>

      </td>
    </tr>
     <tr> 
      <td class="formcolor" ><label for="isuserws">{tr}User workspace{/tr}</label></td>
      <td class="formcolor">
		  <input name="isuserws" id="isuserws" type="checkbox" value="y" {if $workspace.isuserws eq 'y'}checked{/if}>    
      </td>
    </tr>
         <tr> 
      <td class="formcolor" ><label for="hide">{tr}Hide workspace{/tr}</label></td>
      <td class="formcolor">
		  <input name="hide" id="hide" type="checkbox" value="y" {if $workspace.hide eq 'y'}checked{/if}>    
      </td>
    </tr>
     <tr> 
      <td class="formcolor"><label for="owner">{tr}Owner{/tr}</label></td>
      <td class="formcolor"><input name="owner" type="text" id="owner" value="{$workspace.owner}" size="60" maxlength="100"></td>
    </tr>
     <tr> 
      <td class="formcolor" colspan="2"><center><input type="submit" name="send" value="Guardar"></center></td>
    </tr>
  </table>
</form>

<br/>
<table class="findtable">
<tr><td colspan=2>
<b>Workspace path:</b>
{section name=i loop=$path}
<a href="./aulawiki-workspaces.php?viewWS={$path[i].workspaceId}">{$path[i].code}</a>/{/section}

</td></tr>
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="aulawiki-workspaces.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
		 {tr}Number of displayed rows{/tr}
		 <input type="text" size="4" name="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="10%">ID</td>
      <td class="heading" width="10%">Code</td>
      <td class="heading" width="30%"><a class="tableheading" href="aulawiki-workspace.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'nombre_desc'}nombre_asc{else}nombre_desc{/if}">{tr}Name{/tr}</a></td>
      <td class="heading" width="40%">{tr}Description{/tr}</td>
      <td class="heading" width="10%">{tr}Closed{/tr}</td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
    </tr>

{section name=i loop=$workspaces}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
      <td class="{$parImpar}"><a href="./aulawiki-workspaces.php?viewWS={$workspaces[i].workspaceId}">{$workspaces[i].workspaceId}</a></td>
      <td class="{$parImpar}"><a href="./aulawiki-workspaces.php?viewWS={$workspaces[i].workspaceId}">{$workspaces[i].code}</a></td>
      <td class="{$parImpar}"><a href="./aulawiki-workspaces.php?viewWS={$workspaces[i].workspaceId}">{$workspaces[i].name}</a></td>
      <td class="{$parImpar}">{$workspaces[i].description}</td>
      <td class="{$parImpar}">{$workspaces[i].closed}</td>
      <td class="{$parImpar}"> <a class="link" href="aulawiki-workspaces.php?viewWS={$viewWS}&edit={$workspaces[i].workspaceId}">
           <img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a></td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-workspaces.php?viewWS={$viewWS}&delete={$workspaces[i].workspaceId}">
      	   <img src='img/icons2/delete.gif' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-ws_assigned_modules.php?workspaceId={$workspaces[i].workspaceId}&wsmodtype=workspace">
      	   <img src='img/icons/mo.png' border='0' alt='{tr}Assigned modules{/tr}' title='{tr}Assigned modules{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-view_module.php?module=aulawiki_user_groups&workspaceId={$workspaces[i].workspaceId}">
      	   <img src='images/aulawiki/edu_group.gif' border='0' alt='{tr}Users/Groups{/tr}' title='{tr}Users/Groups{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-view_module.php?module=aulawiki_resources&workspaceId={$workspaces[i].workspaceId}">
      	   <img src='img/icons/change.gif' border='0' alt='{tr}Resources{/tr}' title='{tr}Resources{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-workspace_desktop.php?workspaceId={$workspaces[i].workspaceId}">
      	   <img src='img/icons/ico_preview.gif' border='0' alt='{tr}Desktop{/tr}' title='{tr}Desktop{/tr}' /></a>
      </td>
    </tr>
{/section}
</table>


<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="aulawiki-workspaces.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="aulawiki-workspaces.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="aulawiki-workspaces.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>