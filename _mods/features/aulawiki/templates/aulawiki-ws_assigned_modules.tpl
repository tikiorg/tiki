<h1>{tr}Assigned modules{/tr}</h1>
<h2>{tr}{$title}{/tr}</h2>
<a class="linkbut" href="aulawiki-ws_assigned_modules.php?recreate=1&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}">{tr}Restore defaults{/tr}</a><br /><br />
<a class="linkbut" href="aulawiki-ws_assigned_modules.php?clean=1&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}">{tr}Remove all{/tr}</a><br /><br />
<table >
<tr>
	<td >
	<b>{tr}Left column{/tr}</b>
	</td>
	<td >
	<b>{tr}Right column{/tr}</b>
	</td>
</tr>
<tr>
	<!-- left column -->
	<td >
		<table  class="normal">
			<tr>
				<td class="heading">{tr}#{/tr}</td>
				<td class="heading">{tr}name{/tr}</td>
				<td  class="heading">{tr}act{/tr}</td>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$modules_l}
			<tr>
				<td class="{cycle advance=false}">{$modules_l[ix].ord}</td>
				<td class="{cycle advance=false}">({$modules_l[ix].name}) {tr}{$modules_l[ix].title}{/tr}</td>
				<td class="{cycle}">
				  <a class="link" href="aulawiki-ws_assigned_modules.php?up={$modules_l[ix].name}&moduleId={$modules_l[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
  				  <a class="link" href="aulawiki-ws_assigned_modules.php?down={$modules_l[ix].name}&moduleId={$modules_l[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
  				  <a class="link" href="aulawiki-ws_assigned_modules.php?right={$modules_l[ix].name}&moduleId={$modules_l[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/nav_dot_left.gif' alt='{tr}right{/tr}' title='{tr}move to right column{/tr}' border='0' /></a>
				  <a class="link" href="aulawiki-ws_assigned_modules.php?unassign={$modules_l[ix].name}&moduleId={$modules_l[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/delete.gif' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
  				    				 <a class="link" href="aulawiki-ws_assigned_modules.php?edit={$modules_l[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}">
		           <img src='img/icons/edit.gif' border='0' alt='Edit' title='Edit Module' /></a>			
  				  				
				</td>
			</tr>
			{/section}
		</table>
	</td>
	
	<!-- right column -->
	<td >
		<table  class="normal">
			<tr>
				<td class="heading">{tr}#{/tr}</td>
				<td class="heading">{tr}name{/tr}</td>
				<td  class="heading">{tr}act{/tr}</td>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$modules_r}
			<tr>
				<td class="{cycle advance=false}">{$modules_r[ix].ord}</td>
				<td class="{cycle advance=false}">({$modules_r[ix].name}) {tr}{$modules_r[ix].title}{/tr}</td>
				<td class="{cycle}">
				  <a class="link" href="aulawiki-ws_assigned_modules.php?up={$modules_r[ix].name}&moduleId={$modules_r[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
  				  <a class="link" href="aulawiki-ws_assigned_modules.php?down={$modules_r[ix].name}&moduleId={$modules_r[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
  				  <a class="link" href="aulawiki-ws_assigned_modules.php?left={$modules_r[ix].name}&moduleId={$modules_r[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/nav_dot_right.gif' alt='{tr}left{/tr}' title='{tr}move to left column{/tr}' border='0' /></a>
				  <a class="link" href="aulawiki-ws_assigned_modules.php?unassign={$modules_r[ix].name}&moduleId={$modules_r[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}"><img src='img/icons2/delete.gif' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
  				  <a class="link" href="aulawiki-ws_assigned_modules.php?edit={$modules_r[ix].moduleId}&workspaceId={$workspaceId}&wsmodtype={$wsmodtype}">
		           <img src='img/icons/edit.gif' border='0' alt='Edit' title='Edit Module' /></a>			
				</td>
			</tr>
			{/section}
		</table>
	
	</td>
</tr>
</table>

{if $canassign eq 'y'}
<br />
<form action="aulawiki-ws_assigned_modules.php?workspaceId={$workspaceId}&wsmodtype={$wsmodtype}" method="post">
<h2>{tr}Assign module{/tr}</h2>
<table class="normal">
<tr><td class="formcolor"><label for="module">{tr}Module{/tr}:</label></td>
<td class="formcolor">
<select id="module" name="module">
{section name=ix loop=$assignables}
<option value="{$assignables[ix]|escape}" {if $assignables[ix]==$module.name}selected{/if}>{$assignables[ix]}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor"><label for="position">{tr}Column{/tr}:</label></td>
<td class="formcolor">
<select id="position" name="position">
<option value="l" {if "l"==$module.position}selected{/if}>{tr}left{/tr}</option>
<option value="r" {if "r"==$module.position}selected{/if}>{tr}right{/tr}</option>
</select>
</td>
</tr>
<tr>
<td class="formcolor"><label for="order">{tr}Order{/tr}:</label></td>
<td class="formcolor">
<select id="order" name="order">
{section name=ix loop=$orders}
<option value="{$orders[ix]|escape}" {if $orders[ix]==$module.ord}selected{/if}>{$orders[ix]}</option>
{/section}
</select>
</td>
</tr>
<tr> 
  <td class="formcolor"><label for="title">{tr}Title{/tr}</label></td>
  <td class="formcolor"><input name="title" type="text" id="title" value="{$module.title}" size="40" maxlength="250"></td>
</tr>
{*
<tr> 
  <td class="formcolor"><label for="cache_time">{tr}Cache time{/tr}</label></td>
  <td class="formcolor"><input name="cache_time" type="text" id="cache_time" value="{$module.cache_time}" size="40" maxlength="250"></td>
</tr>
<tr> 
  <td class="formcolor"><label for="rows">{tr}Rows{/tr}</label></td>
  <td class="formcolor"><input name="rows" type="text" id="rows" value="{$module.rows}" size="40" maxlength="250"></td>
</tr>
*}
<tr> 
  <td class="formcolor"><label for="params">{tr}Params{/tr}</label></td>
  <td class="formcolor"><input name="params" type="text" id="params" value="{$module.params}" size="40" maxlength="250">(Eg. name=WikiPageName)</td>
</tr>
{*
<tr> 
  <td class="formcolor"><label for="groups">{tr}Groups{/tr}</label></td>
  <td class="formcolor"><input name="groups" type="text" id="groups" value="{$module.groups}" size="40" maxlength="250"></td>
</tr>
*}
<tr>
<td class="formcolor">&nbsp;</td>
<td class="formcolor">
<input type="hidden" name="moduleId" value="{$module.moduleId}" />
<input type="submit" name="assign" value="{tr}assign{/tr}" /></td>
</tr>
</table>
</form>
{/if}
