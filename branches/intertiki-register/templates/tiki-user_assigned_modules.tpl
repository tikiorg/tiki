<h1><a class="pagetitle" href="tiki-user_assigned_modules.php">{tr}User assigned modules{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}UserAssignedModules" target="tikihelp" class="tikihelp" title="{tr}User Assigned Modules{/tr}">
{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_assigned_modules.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Assigned Modules tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>
{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
{/if}
<div class="navbar">
<a class="linkbut" href="tiki-user_assigned_modules.php?recreate=1">{tr}Restore defaults{/tr}</a>
</div>
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
				<td class="heading">{tr}Name{/tr}</td>
				<td  class="heading">{tr}act{/tr}</td>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$modules_l}
			<tr>
				<td class="{cycle advance=false}">{$modules_l[ix].ord}</td>
				<td class="{cycle advance=false}">{$modules_l[ix].name}</td>
				<td class="{cycle}">
				  <a class="link" href="tiki-user_assigned_modules.php?up={$modules_l[ix].moduleId}">{icon _id='resultset_up'}</a>
  				  <a class="link" href="tiki-user_assigned_modules.php?down={$modules_l[ix].moduleId}">{icon _id='resultset_down'}</a>
  				  <a class="link" href="tiki-user_assigned_modules.php?right={$modules_l[ix].moduleId}">{icon _id='resultset_next' alt='{tr}Right{/tr}' title='{tr}Move to Right Column{/tr}'}</a>
				  {if $modules_l[ix].name ne 'application_menu' and $modules_l[ix].name ne 'login_box' and $modules_l[ix].type ne 'P'}
  					<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_l[ix].moduleId}">{icon _id='cross' alt='{tr}Unassign{/tr}'}</a> 
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
				<td class="heading">{tr}Name{/tr}</td>
				<td  class="heading">{tr}act{/tr}</td>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$modules_r}
			<tr>
				<td class="{cycle advance=false}">{$modules_r[ix].ord}</td>
				<td class="{cycle advance=false}">{$modules_r[ix].name}</td>
				<td class="{cycle}">
				  <a class="link" href="tiki-user_assigned_modules.php?up={$modules_r[ix].moduleId}">{icon _id='resultset_up'}</a>
  				  <a class="link" href="tiki-user_assigned_modules.php?down={$modules_r[ix].moduleId}">{icon _id='resultset_down'}</a>
  				  <a class="link" href="tiki-user_assigned_modules.php?left={$modules_r[ix].moduleId}">{icon _id='resultset_previous' alt='{tr}Left{/tr}' title='{tr}Move to Left Column{/tr}'}</a>
				  {if $modules_r[ix].name ne 'application_menu' and $modules_r[ix].name ne 'login_box' and $modules_r[ix].type ne 'P'}
  					<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_r[ix].moduleId}">{icon _id='cross' alt='{tr}Unassign{/tr}'}</a> 
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
<option value="{$assignables[ix].moduleId|escape}">{$assignables[ix].name}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">{tr}Column{/tr}:</td>
<td class="formcolor">
<select name="position">
<option value="l">{tr}Left{/tr}</option>
<option value="r">{tr}Right{/tr}</option>
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
<td class="formcolor"><input type="submit" name="assign" value="{tr}Assign{/tr}" /></td>
</tr>
</table>
</form>
{/if}
