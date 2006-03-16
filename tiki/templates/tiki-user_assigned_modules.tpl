<h1><a class="pagetitle" href="tiki-user_assigned_modules.php">{tr}User assigned modules{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}UserAssignedModules" target="tikihelp" class="tikihelp" title="{tr}User Assigned Modules{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_assigned_modules.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Assigned Modules tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}' /></a>
{/if}</h1>

{include file=tiki-mytiki_bar.tpl}
<br /><br />
<a class="linkbut" href="tiki-user_assigned_modules.php?recreate=1">{tr}Restore defaults{/tr}</a><br /><br />
<h2>{tr}User assigned modules{/tr}</h2>
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
				<td class="{cycle advance=false}">{$modules_l[ix].name}</td>
				<td class="{cycle}">
				  <a class="link" href="tiki-user_assigned_modules.php?up={$modules_l[ix].name}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-user_assigned_modules.php?down={$modules_l[ix].name}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-user_assigned_modules.php?right={$modules_l[ix].name}"><img src='img/icons2/nav_dot_left.gif' alt='{tr}right{/tr}' title='{tr}move to right column{/tr}' border='0' /></a>
				  {if $modules_l[ix].name ne 'application_menu' and $modules_l[ix].name ne 'login_box' and $modules_l[ix].type ne 'P'}
  					<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_l[ix].name}"><img src='img/icons2/delete.gif' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
  				  {/if}

				
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
				<td class="{cycle advance=false}">{$modules_r[ix].name}</td>
				<td class="{cycle}">
				  <a class="link" href="tiki-user_assigned_modules.php?up={$modules_r[ix].name}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-user_assigned_modules.php?down={$modules_r[ix].name}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-user_assigned_modules.php?left={$modules_r[ix].name}"><img src='img/icons2/nav_dot_right.gif' alt='{tr}left{/tr}' title='{tr}move to left column{/tr}' border='0' /></a>
				  {if $modules_r[ix].name ne 'application_menu' and $modules_r[ix].name ne 'login_box' and $modules_r[ix].type ne 'P'}
  					<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_r[ix].name}"><img src='img/icons2/delete.gif' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
  				  {/if}

				
				</td>
			</tr>
			{/section}
		</table>
	
	</td>
</tr>
</table>

{if $canassign eq 'y'}
<br />
<form action="tiki-user_assigned_modules.php" method="post">
<h2>{tr}Assign module{/tr}</h2>
<table class="normal">
<tr><td class="formcolor">{tr}Module{/tr}:</td>
<td class="formcolor">
<select name="module">
{section name=ix loop=$assignables}
<option value="{$assignables[ix].name|escape}">{$assignables[ix].name}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">{tr}Column{/tr}:</td>
<td class="formcolor">
<select name="position">
<option value="l">{tr}left{/tr}</option>
<option value="r">{tr}right{/tr}</option>
</select>
</td>
</tr>
<tr>
<td class="formcolor">{tr}Order{/tr}:</td>
<td class="formcolor">
<select name="order">
{section name=ix loop=$orders}
<option value="{$orders[ix]|escape}">{$orders[ix]}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">&nbsp;</td>
<td class="formcolor"><input type="submit" name="assign" value="{tr}assign{/tr}" /></td>
</tr>
</table>
</form>
{/if}
