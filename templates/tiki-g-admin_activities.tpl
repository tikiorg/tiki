{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_activities.php?pid={$pid}">{tr}Admin process activities{/tr}</a></h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}To learn more about the <a class="rbox-link" target="tikihelp" href="http://workflow.tikiwiki.org">Galaxia workflow engine</a>{/tr}
</div>
</div>
<br />

{include file=tiki-g-proc_bar.tpl}

{if count($errors) > 0}
<div class="wikitext">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

<h2>{tr}Add or edit an activity{/tr} <a class="link" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activityId=0">{tr}New{/tr}</a></h2>
<form action="tiki-g-admin_activities.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}Name{/tr}</td>
  <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}Description{/tr}</td>
  <td class="formcolor"><textarea name="description" rows="4" cols="60">{$info.description|escape}</textarea></td>
</tr>
<tr>  
  <td class="formcolor">{tr}Type{/tr}</td>
  <td class="formcolor">
  <select name="type">
  <option value="start" {if $info.type eq 'start'}selected="selected"{/if}>{tr}Start{/tr}</option>
  <option value="end" {if $info.type eq 'end'}selected="selected"{/if}>{tr}End{/tr}</option>		  
  <option value="activity" {if $info.type eq 'activity'}selected="selected"{/if}>{tr}activity{/tr}</option>		  
  <option value="switch" {if $info.type eq 'switch'}selected="selected"{/if}>{tr}Switch{/tr}</option>		  
  <option value="split" {if $info.type eq 'split'}selected="selected"{/if}>{tr}split{/tr}</option>		  
  <option value="join" {if $info.type eq 'join'}selected="selected"{/if}>{tr}join{/tr}</option>		  
  <option value="standalone" {if $info.type eq 'standalone'}selected="selected"{/if}>{tr}standalone{/tr}</option>		  
  </select>
  {tr}interactive{/tr}:<input type="checkbox" name="isInteractive" {if $info.isInteractive eq 'y'}checked="checked"{/if} />
  {tr}auto routed{/tr}:<input type="checkbox" name="isAutoRouted" {if $info.isAutoRouted eq 'y'}checked="checked"{/if} />
  </td>
</tr>
<tr>
  <td class="formcolor">{tr}Expiration Time{/tr} </td>
  <td class="formcolor">
  {tr}Years{/tr}:
  <SELECT name="year" size ="1">
  	{html_options options=$years selected=$info.year}
  </SELECT>
  {tr}Months{/tr}:
  <SELECT name="month" size="1">
  	{html_options options=$months selected=$info.month}
  </SELECT>
  {tr}Days{/tr}:
  <SELECT name="day" size="1">
  	{html_options options=$days selected=$info.day}
  </SELECT>
  {tr}Hours{/tr}:
  <SELECT name="hour" size="1">
  	{html_options options=$hours selected=$info.hour}
  </SELECT>
  {tr}Minutes{/tr}:
  <SELECT name="minute" size="1">
  	{html_options options=$minutes selected=$info.minute}
  </SELECT>
  </td>
</tr>
<tr>
  <td class="formcolor">{tr}Add transitions{/tr}</td>
  <td class="formcolor">
    <table class="normal">
		<tr>
			<td class="formcolor">
				{tr}Add transition from:{/tr}<br />
				<select name="add_tran_from[]" multiple="multiple" size="5">
				{section name=ix loop=$items}
				<option value="{$items[ix].activityId|escape}" {if $items[ix].from eq 'y'}selected="selected"{/if}>{$items[ix].name|adjust:30}</option>
				{/section}			
				</select>
			</td>
			<td class="formcolor">
				{tr}Add transition to:{/tr}<br />
				<select name="add_tran_to[]" multiple="multiple" size="5">
				{section name=ix loop=$items}
				<option value="{$items[ix].activityId|escape}" {if $items[ix].to eq 'y'}selected="selected"{/if}>{$items[ix].name|adjust:30}</option>
				{/section}			
				</select>
			</td>
		</tr>    
    </table>
  </td>
</tr>

<tr>
  <td class="formcolor">{tr}Roles{/tr}</td>
  <td class="formcolor">
  {section name=ix loop=$roles}
  {$roles[ix].name}[<a class="link" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;where={$where}&amp;activityId={$info.activityId}&amp;pid={$pid}&amp;remove_role={$roles[ix].roleId}">x</a>]<br />
  {sectionelse}
  {tr}No roles associated to this activity{/tr}
  {/section}
  </td>
</tr>
<tr>
  <td class="formcolor">{tr}Add Role{/tr}</td>
  <td class="formcolor">
  {if count($all_roles)}
  <select name="userole">
  <option value="">{tr}Add New{/tr}</option>
  {section loop=$all_roles name=ix}
  <option value="{$all_roles[ix].roleId|escape}">{$all_roles[ix].name}</option>
  {/section}
  </select>
  {/if}
  <input type="text" name="rolename" /><input type="submit" name="addrole" value="{tr}Add Role{/tr}" />
  </td>
</tr>
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="save_act" value="{tr}Save{/tr}" /> </td>
</tr>

</table>
</form>

<h2>{tr}Process activities{/tr}</h2>
	
<form action="tiki-g-admin_activities.php" method="post">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<table>
<tr>
	<td>
		{tr}Find{/tr}
	</td>	
	<td>
		{tr}Type{/tr}
	</td>
	<td>
		{tr}Int{/tr}
	</td>
	<td>
		{tr}Routing{/tr}
	</td>
	<td>
		{tr}Role{/tr}
	</td>
	<td>
		&nbsp;
	</td>
</tr>			
<tr>
	<td>	
		<input size="8" type="text" name="find" value="{$find|escape}" />
	</td>
	<td>
		<select name="filter_type">
		  <option value="">{tr}All{/tr}</option>
		  <option value="start">{tr}Start{/tr}</option>
		  <option value="end" >{tr}End{/tr}</option>		  
		  <option value="activity" >{tr}activity{/tr}</option>		  
		  <option value="switch" >{tr}Switch{/tr}</option>		  
		  <option value="split" >{tr}split{/tr}</option>		  
		  <option value="join" >{tr}join{/tr}</option>		  
		  <option value="standalone" >{tr}standalone{/tr}</option>		  
		</select>
	</td>
	<td>
		<select name="filter_interactive">
		<option value="">{tr}All{/tr}</option>
		<option value="y">{tr}Interactive{/tr}</option>
		<option value="n">{tr}Automatic{/tr}</option>
		</select>
	</td>
	<td>
		<select name="filter_autoroute">
		<option value="">{tr}All{/tr}</option>
		<option value="y">{tr}Auto routed{/tr}</option>
		<option value="n">{tr}Manual{/tr}</option>
		</select>
	</td>
	<td>
		<select name="filter_role">
		<option value="">{tr}All{/tr}</option>
		{section loop=$all_roles name=ix}
		<option value="{$all_roles[ix].roleId|escape}">{$all_roles[ix].name}</option>
		{/section}
		</select>
	</td>
	<td>
		<input type="submit" name="filter" value="{tr}Filter{/tr}" />
	</td>
</tr>
</table>	
</form>
<form action="tiki-g-admin_activities.php" method="post">
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="where2" value="{$where2|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
<table class="normal">
<tr>
<td style="text-align:center;"  class="heading"><input type="submit" name="delete_act" value="x " /></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'flowNum_desc'}flowNum_asc{else}flowNum_desc{/if}">{tr}#{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'isInteractive_desc'}isInteractive_asc{else}isInteractive_desc{/if}">{tr}inter{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'isInteractive_desc'}isAutoRouted_asc{else}isAutoRouted_desc{/if}">{tr}route{/tr}</a></td>
<td  class="heading" >{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:center;" class="{cycle advance=false}">
		<input type="checkbox" name="activity[{$items[ix].activityId}]" />
	</td>
	<td style="text-align:right;" class="{cycle advance=false}">
	  {$items[ix].flowNum}
	</td>

	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activityId={$items[ix].activityId}">{$items[ix].name}</a>
	  {if $items[ix].roles < 1}
		<small>{tr}(no roles){/tr}</small>
	  {/if}
	</td>
	<td style="text-align:center;" class="{cycle advance=false}">
	  {$items[ix].type|act_icon:$items[ix].isInteractive}
	</td>
	<td style="text-align:center;" class="{cycle advance=false}">
	  <input type="checkbox" name="activity_inter[{$items[ix].activityId}]" {if $items[ix].isInteractive eq 'y'}checked="checked"{/if} />
	</td>
    <td style="text-align:center;" class="{cycle advance=false}">
	  <input type="checkbox" name="activity_route[{$items[ix].activityId}]" {if $items[ix].isAutoRouted eq 'y'}checked="checked"{/if} />
	</td>

	<td class="{cycle}">
	<a class="link" href="tiki-g-admin_shared_source.php?pid={$pid}&amp;activityId={$items[ix].activityId}">{tr}Code{/tr}</a>
	{if $items[ix].isInteractive eq 'y'}
	<br /><a class="link" href="tiki-g-admin_shared_source.php?pid={$pid}&amp;activityId={$items[ix].activityId}&amp;template=1">{tr}template{/tr}</a>
	{/if}
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="6">
	{tr}No activities defined yet{/tr}
	</td>
</tr>	
{/section}
<tr>
<td class="heading" colspan="7" style="text-align:center;">
<input type="submit" name="update_act" value="{tr}Update{/tr}" />
</td>
</tr>
</table>
</form>	

<h2>{tr}Process Transitions{/tr}</h2>
<table class="normal">
<tr>
	<td >
		<h3>{tr}List of transitions{/tr}</h3>
			<form action="tiki-g-admin_activities.php" method="post" id='filtran'>
			<input type="hidden" name="pid" value="{$pid|escape}" />
			<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
			<input type="hidden" name="find" value="{$find2|escape}" />
			<input type="hidden" name="where" value="{$where2|escape}" />
			<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
			{tr}From:{/tr}<select name="filter_tran_name" onchange="javascript:document.getElementById('filtran').submit();">
			<option value="" {if $filter_tran_name eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
			{section name=ix loop=$items}
			<option value="{$items[ix].activityId|escape}" {if $filter_tran_name eq $items[ix].activityId}selected="selected"{/if}>{$items[ix].name}</option>
			{/section}
			</select>
<!--			<input type="submit" name="filter_tran" value="{tr}Filter{/tr}" /> -->
			</form>
			
			<form action="tiki-g-admin_activities.php" method="post">
			<input type="hidden" name="pid" value="{$pid|escape}" />
			<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
			<input type="hidden" name="find" value="{$find2|escape}" />
			<input type="hidden" name="where" value="{$where2|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
			<input type="hidden" name="where2" value="{$where2|escape}" />
			<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
			<table class="normal">
			<tr>
			<td class="heading" ><input type="submit" name="delete_tran" value="{tr}x{/tr} " /></td>
			<td class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'actFromName_desc'}actFromName_asc{else}actFromName_desc{/if}">{tr}Origin{/tr}</a></td>
			<!--<td class="heading" ><a class="tableheading" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={if $sort_mode eq 'actToName_desc'}actToName_asc{else}actToName_desc{/if}">{tr}To{/tr}</a></td>-->
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$transitions}
			<tr>
				<td class="{cycle advance=false}">
					<input type="checkbox" name="transition[{$transitions[ix].actFromId}_{$transitions[ix].actToId}]" />
				</td>
				<td class="{cycle advance=false}">
					<a class="link" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activityId={$transitions[ix].actFromId}">{$transitions[ix].actFromName}</a>
					<img src='lib/Galaxia/img/icons/next.gif' alt='to' />
					<a class="link" href="tiki-g-admin_activities.php?where2={$where2}&amp;sort_mode2={$sort_mode2}&amp;pid={$pid}&amp;find={$find}&amp;where={$where}&amp;sort_mode={$sort_mode}&amp;activityId={$transitions[ix].actToId}">{$transitions[ix].actToName}</a>
				</td>
				<!--
				<td class="{cycle advance=false}">
					{$transitions[ix].actToName}
				</td>
				-->
			</tr>
			{sectionelse}
			<tr>
				<td class="{cycle advance=false}" colspan="3">
				{tr}No transitions defined yet{/tr}
				</td>
			</tr>
			{/section}
			</table>
			</form>		
	</td>
	<td class="formcolor" >
		<h3>{tr}Add a transition{/tr}</h3>
		<form action="tiki-g-admin_activities.php" method="post">
		<input type="hidden" name="pid" value="{$pid|escape}" />
		<input type="hidden" name="activityId" value="{$info.activityId|escape}" />
		<input type="hidden" name="find" value="{$find2|escape}" />
		<input type="hidden" name="where" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
		<input type="hidden" name="where2" value="{$where2|escape}" />
		<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
		<table class="normal">
		<tr>
		  <td class="formcolor">
		  From:
		  </td>
		  <td>
		  <select name="actFromId">
		  {section name=ix loop=$items}
		  <option value="{$items[ix].activityId|escape}">{$items[ix].name}</option>
		  {/section}
		  </select>
		  </td>
		</tr>
		<tr>
		  <td class="formcolor">
		  To: 
		  </td>
		  <td>
		   <select name="actToId">
		  {section name=ix loop=$items}
		  <option value="{$items[ix].activityId|escape}">{$items[ix].name}</option>
		  {/section}
		  </select>
		  </td>
		</tr>
		<tr>
		  <td class="formcolor">&nbsp;</td>
		  <td class="formcolor">
		    <input type="submit" name="add_trans" value="{tr}Add{/tr}" />
		  </td>
		</tr>
		</table>	
		</form>
	</td>
</tr>
</table>	
	
