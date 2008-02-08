{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-map_roles.php?pid={$pid}">{tr}Map process roles{/tr}</a></h1>
<a class="linkbut" href="tiki-g-admin_processes.php">{tr}Admin Processes{/tr}</a>
<a class="linkbut" href="tiki-g-admin_activities.php?pid={$pid}">{tr}Admin Activities{/tr}</a>
<a class="linkbut" href="tiki-g-admin_roles.php?pid={$pid}">{tr}Admin Roles{/tr}</a>
<a class="linkbut" href="tiki-g-admin_processes.php?pid={$pid}">{tr}Edit this Process{/tr}</a><br /><br />
{tr}Process:{/tr} {$proc_info.name} {$proc_info.version}<br />

process graph<br />

{if count($errors) > 0}
<div class="wikitext">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

{if count($roles) > 0}
	<h2>{tr}Map users to roles{/tr}</h2>
	<form method="post" action="tiki-g-map_roles.php">
	<input type="hidden" name="pid" value="{$pid|escape}" />
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<table class="normal">
	<tr>
		<td class="formcolor">{tr}Map{/tr}</td>
		<td class="formcolor">
		  <table border="1" >
		  	<tr>
		  		<td class="formcolor" >
		  		{tr}Users{/tr}:
				<input type="text" size="10" name="find_users" value="{$find_users|escape}" />
				<input type="submit" name="findusers" value="{tr}Filter{/tr}" />	  
		  		</td>
		  		<td class="formcolor" >
	  			{tr}Roles{/tr}:<br />		  		
		  		</td>
		  	</tr>
		  	<tr>
		  		<td class="formcolor" >
					<select name="user[]" multiple="multiple" size="10">
					{section name=ix loop=$users}
					<option value="{$users[ix].user|escape}">{$users[ix].user}</option>
					{/section}
					</select>
		  		</td>
		  		<td class="formcolor" >

					<select name="role[]" multiple="multiple" size="10">
					{section name=ix loop=$roles}
					<option value="{$roles[ix].roleId|escape}">{$roles[ix].name}</option>
					{/section}
					</select>	  		
		  		</td>
		  	</tr>
		  </table>
		</td>
	</tr>
	
	<tr>
		<td class="formcolor">&nbsp;</td>
		<td class="formcolor">
			<input type="submit" name="save" value="{tr}map{/tr}" />
		</td>
	</tr>
	</table>
	</form>
{else}
	<h2>{tr}Warning{/tr}</h2>
	{tr}No roles are defined yet so no roles can be mapped{/tr}<br />
{/if}

<h2>{tr}List of mappings{/tr}</h2>
<form action="tiki-g-map_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find|escape}" />
<input type="submit" name="filter" value="{tr}Find{/tr}" />
</form>
<form action="tiki-g-map_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td class="heading"><input type="submit" name="delete" value="{tr}Del{/tr}" /></td>
<td class="heading" ><a class="tableheading" href="tiki-g-map_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-g-map_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
		<input type="checkbox" name="map[{$items[ix].user}:::{$items[ix].roleId}]" />
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].name}
	</td>
	<td class="{cycle}">
	  {$items[ix].user}
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="3">
	{tr}No mappings defined yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
