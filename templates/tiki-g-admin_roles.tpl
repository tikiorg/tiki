{*Smarty template*}
<a class="pagetitle" href="tiki-g-admin_roles.php?pid={$pid}">{tr}Admin process roles{/tr}</a><br/><br/>
{include file=tiki-g-proc_bar.tpl}
{if count($errors) > 0}
<div class="wikitext">
Errors:<br/>
{section name=ix loop=$errors}
<small>{$errors[ix]}</small><br/>
{/section}
</div>
{/if}

<h3>{tr}Add or edit a role{/tr} <a class="link" href="tiki-g-admin_roles.php?pid={$pid}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;sort_mode2={$sort_mode2}&amp;find={$find}&amp;roleId=0">{tr}new{/tr}</a></h3>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid}" />
<input type="hidden" name="roleId" value="{$info.roleId}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="offset" value="{$offset}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}name{/tr}</td>
  <td class="formcolor"><input type="text" name="name" value="{$info.name}" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}description{/tr}</td>
  <td class="formcolor"><textarea name="description" rows="4" cols="60">{$info.description}</textarea></td>
</tr>
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /> </td>
</tr>
</table>
</form>

<h3>{tr}Process roles{/tr}</h3>
	

<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="pid" value="{$pid}" />
<input type="hidden" name="roleId" value="{$info.roleId}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="offset" value="{$offset}" />
<table class="normal">
<tr>
<td width="5%" class="heading"><input type="submit" name="delete" value="{tr}del{/tr}" /></td>
<td width="20%" class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td width="75%" class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?sort_mode={$sort_mode}&amp;pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode2={if $sort_mode2 eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
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
	<h3>{tr}Map users to roles{/tr}</h3>
	<form method="post" action="tiki-g-admin_roles.php">
	<input type="hidden" name="pid" value="{$pid}" />
	<input type="hidden" name="offset" value="{$offset}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode}" />
	<input type="hidden" name="sort_mode2" value="{$sort_mode2}" />
	<input type="hidden" name="find" value="{$find}" />
	<table class="normal">
	<tr>
		<td class="formcolor">{tr}Map{/tr}</td>
		<td class="formcolor">
		  <table border="1" width="100%">
		  	<tr>
		  		<td class="formcolor" width="50%">
		  		{tr}Users{/tr}:
				<input type="text" size="10" name="find_users" value="{$find_users}" />
				<input type="submit" name="findusers" value="{tr}filter{/tr}" />	  
		  		</td>
		  		<td class="formcolor" width="50%">
	  			{tr}Roles{/tr}:<br/>		  		
		  		</td>
		  	</tr>
		  	<tr>
		  		<td class="formcolor" width="50%">
					<select name="user[]" multiple="multiple" size="10">
					{section name=ix loop=$users}
					<option value="{$users[ix].user}">{$users[ix].user|adjust:30}</option>
					{/section}
					</select>
		  		</td>
		  		<td class="formcolor" width="50%">

					<select name="role[]" multiple="multiple" size="10">
					{section name=ix loop=$roles}
					<option value="{$roles[ix].roleId}">{$roles[ix].name|adjust:30}</option>
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
{else}
	<h3>{tr}Warning{/tr}</h3>
	{tr}No roles are defined yet so no roles can be mapped{/tr}<br/>
{/if}

<h3>{tr}List of mappings{/tr}</h3>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid}" />
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find}" />
<input type="submit" name="filter" value="{tr}find{/tr}" />
</form>
<form action="tiki-g-admin_roles.php" method="post">
<input type="hidden" name="pid" value="{$pid}" />
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="sort_mode2" value="{$sort_mode2}" />
<table class="normal">
<tr>
<td width="4%" class="heading"><input type="submit" name="delete_map" value="{tr}del{/tr}" /></td>
<td width="48%" class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Role{/tr}</a></td>
<td width="48%" class="heading" ><a class="tableheading" href="tiki-g-admin_roles.php?pid={$pid}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
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
	  {$mapitems[ix].user}
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
[<a class="prevnext" href="tiki-g-admin_roles.php?sort_mode2={$sort_mode2}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-g-admin_roles.php?sort_mode2={$sort_mode2}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-g-admin_roles.php?sort_mode2={$sort_mode2}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
