{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_roles.php?pid={$pid}">{tr}Admin process roles{/tr}</a></h1>
{include file=tiki-g-proc_bar.tpl}
{if count($errors) > 0}
<div class="wikitext">
Errors:<br />
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br />
{/section}
</div>
{/if}

<h2>{tr}Add or edit a role{/tr} <a class="link" href="tiki-g-admin_roles.php?pid={$pid}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;sort_mode2={$sort_mode2}&amp;find={$find}&amp;roleId=0">{tr}New{/tr}</a></h2>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="roleId" value="{$info.roleId|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
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
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /> </td>
</tr>
</table>
</form>

<h2>{tr}Process roles{/tr}</h2>
	

<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="roleId" value="{$info.roleId|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<table class="normal">
<tr>
<td  class="heading"><input type="submit" name="delete" value="{tr}x{/tr} " /></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
		<input type="checkbox" name="role[{$items[ix].roleId}]" />
	</td>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_roles.php?sort_mode={$sort_mode}&amp;offset={$offset}&amp;find={$find}&amp;pid={$pid}&amp;sort_mode2={$sort_mode2}&amp;roleId={$items[ix].roleId}">{$items[ix].name}</a>
	</td>
	<td class="{cycle}">
	  {$items[ix].description}
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="3">
	{tr}No roles defined yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>	


{if count($roles) > 0}
	<h2>{tr}Map users to roles{/tr}</h2>
	<form method="post" action="tiki-g-admin_roles.php">
	<input type="hidden" name="pid" value="{$pid|escape}" />
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
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
					<option value="Anonymous">"{tr}Anonymous{/tr}"</option>
					{section name=ix loop=$users}
					<option value="{$users[ix].user|escape}">{$users[ix].user|adjust:30}</option>
					{/section}
					</select>
		  		</td>
		  		<td class="formcolor" >

					<select name="role[]" multiple="multiple" size="10">
					{section name=ix loop=$roles}
					<option value="{$roles[ix].roleId|escape}">{$roles[ix].name|adjust:30}</option>
					{/section}
					</select>	  		
		  		</td>
		  	</tr>
		  </table>
		</td>
	</tr>
	
	<tr>
		<td class="formcolor">&nbsp;</td>
		<td style="text-align:center;" class="formcolor">
			<input type="submit" name="save_map" value="{tr}map{/tr}" />
		</td>
	</tr>
	</table>
	</form>
	
	{* GROUPS *}
	<h2>{tr}Map groups to roles{/tr}</h2>
	<form method="post" action="tiki-g-admin_roles.php">
	<input type="hidden" name="pid" value="{$pid|escape}" />
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<table class="normal">
	<tr>
		<td class="formcolor">
		{tr}Operation{/tr}
		</td>
		<td class="formcolor">
		{tr}Group{/tr}
		</td>
		<td class="formcolor">
		{tr}Role{/tr}
		</td>
		<td class="formcolor">
		&nbsp;
		</td>
	</tr>
	<tr>
		<td class="formcolor">
			<select name="op">
			<option value="add">{tr}Add{/tr}</option>
			<option value="remove">{tr}Remove{/tr}</option>
			</select>
		</td>

		<td class="formcolor">
			<select name="group">
			{section name=ix loop=$groups}
			{if $groups[ix] != "Anonymous"}
			<option value="{$groups[ix]|escape}">{$groups[ix]}</option>
			{/if}
			{/section}
			</select>
		</td>

		<td class="formcolor">
			<select name="role">
			{section name=ix loop=$roles}
			<option value="{$roles[ix].roleId|escape}">{$roles[ix].name|adjust:30}</option>
			{/section}
			</select>	  		
		</td>
		<td class="formcolor">
			<input type="submit" name="mapg" value="{tr}Go{/tr}" />
		</td>

	</tr>
	</table>
	</form>

	
	
{else}
	<h2>{tr}Warning{/tr}</h2>
	{tr}No roles are defined yet so no roles can be mapped{/tr}<br />
{/if}

<h2>{tr}List of mappings{/tr}</h2>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find|escape}" />
<input type="submit" name="filter" value="{tr}Find{/tr}" />
</form>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2|escape}" />
<table class="normal">
<tr>
<td  class="heading"><input type="submit" name="delete_map" value="{tr}x{/tr} " /></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$mapitems}
<tr>
	<td class="{cycle advance=false}">
		<input type="checkbox" name="map[{$mapitems[ix].user}:::{$mapitems[ix].roleId}]" />
	</td>
	<td class="{cycle advance=false}">
	  {$mapitems[ix].name}
	</td>
	<td class="{cycle}">
	  {if $mapitems[ix].user eq ''}
	  "{tr}Anonymous{/tr}"
	  {else}
	  {$mapitems[ix].user}
	  {/if}
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

