{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<h1>{tr}Assigned modules{/tr}</h1>
<h2>{tr}{$title}{/tr}</h2>
<a class="linkbut" href="tiki-workspaces_assigned_modules.php?recreate=1&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}">{tr}Restore defaults{/tr}</a><br /><br />
<a class="linkbut" href="tiki-workspaces_assigned_modules.php?clean=1&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}">{tr}Remove all{/tr}</a><br /><br />
<table >
<tr>
{foreach key=key item=column from=$columns}
	<td >
	<b>{tr}{$column}{/tr}</b>
	</td>
{/foreach}
</tr>
<tr>
{foreach key=key item=column from=$columns}
	<td >
		<table  class="normal">
			<tr>
				<td class="heading">{tr}#{/tr}</td>
				<td class="heading">{tr}name{/tr}</td>
				<td  class="heading">{tr}act{/tr}</td>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=ix loop=$modules[$key]}
			<tr>
				<td class="{cycle advance=false}">{$modules[$key][ix].ord}</td>
				<td class="{cycle advance=false}">({$modules[$key][ix].name}) {tr}{$modules[$key][ix].title}{/tr}</td>
				<td class="{cycle}">
				  				  <a class="link" href="tiki-workspaces_assigned_modules.php?left={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}"><img src='img/icons2/nav_dot_right.gif' alt='{tr}left{/tr}' title='{tr}move to left column{/tr}' border='0' /></a>
				  <a class="link" href="tiki-workspaces_assigned_modules.php?up={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-workspaces_assigned_modules.php?down={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
  				  <a class="link" href="tiki-workspaces_assigned_modules.php?right={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}"><img src='img/icons2/nav_dot_left.gif' alt='{tr}right{/tr}' title='{tr}move to right column{/tr}' border='0' /></a>
				  <a class="link" href="tiki-workspaces_assigned_modules.php?unassign={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}"><img src='img/icons2/delete.gif' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
  				    				 <a class="link" href="tiki-workspaces_assigned_modules.php?edit={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}">
		           <img src='img/icons/edit.gif' border='0' alt='Edit' title='Edit Module' /></a>			
  				  				
				</td>
			</tr>
			{/section}
		</table>
	</td>
{/foreach}	
</tr>
</table>

{if $canassign eq 'y'}
<br />
<form name="moduleParamsForm" id="moduleParamsForm" action="tiki-workspaces_assigned_modules.php?wstypeId={$wstypeId}&wsmodtype={$wsmodtype}" method="post">
<input name="selectModule" id="selectModule" type="hidden" value="">
<h2>{tr}Assign module{/tr}</h2>
<table class="normal">
<tr><td class="formcolor"><label for="module">{tr}Module{/tr}:</label></td>
<td class="formcolor">
<div class="fieldHelp">
<select id="module" name="module" onchange="selectModule.value='y';moduleParamsForm.submit()">
{section name=ix loop=$assignables}
<option value="{$assignables[ix]|escape}" {if $assignables[ix]==$module.name}selected{/if}>{$assignables[ix]}</option>
{/section}
</select>
</div>
  <div class="fieldHelp">{$module.help}</div>
</td>
</tr>
<tr>
<td class="formcolor"><label for="position">{tr}Column{/tr}:</label></td>
<td class="formcolor">
<select id="position" name="position">
{foreach key=key item=column from=$columns}
<option value="{$key}" {if "$key"==$module.position}selected{/if}>{tr}{$column}{/tr}</option>
{/foreach}
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
  <td class="formcolor"><label for="title">{tr}Title{/tr}:</label></td>
  <td class="formcolor"><input name="title" type="text" id="title" value="{$module.title}" size="40" maxlength="240"></td>
</tr>
{*
<tr> 
  <td class="formcolor"><label for="cache_time">{tr}Cache time{/tr}:</label></td>
  <td class="formcolor"><input name="cache_time" type="text" id="cache_time" value="{$module.cache_time}" size="40" maxlength="240"></td>
</tr>
<tr> 
  <td class="formcolor"><label for="rows">{tr}Rows{/tr}:</label></td>
  <td class="formcolor"><input name="rows" type="text" id="rows" value="{$module.rows}" size="40" maxlength="240"></td>
</tr>
*}
{*
<tr> 
  <td class="formcolor"><label for="groups">{tr}Groups{/tr}:</label></td>
  <td class="formcolor"><input name="groups" type="text" id="groups" value="{$module.groups}" size="40" maxlength="240"></td>
</tr>
*}
<tr> 
  <td class="formcolor"><label for="style">{tr}Title Style{/tr}:</label></td>
  <td class="formcolor"><input name="style_title" type="text" id="style_title" value="{$module.style_title}" size="40" maxlength="240"></td>
</tr>
<tr> 
  <td class="formcolor"><label for="style">{tr}Data Style{/tr}:</label></td>
  <td class="formcolor"><input name="style_data" type="text" id="style_data" value="{$module.style_data}" size="40" maxlength="240"></td>
</tr>
{if $arrayparams}
	<tr> 
	  <td colspan=2 class="formcolor"><label>{tr}Module params{/tr}</label></td>
	</tr>
  {foreach key=key item=param from=$arrayparams}
    {if !$param.hide && $param.hide!="y"}
		<tr> 
		  <td class="formcolor"><label for="module_param_{$key}">{tr}{$param.name}{/tr}:</label></td>
		  <td class="formcolor">
		    <div class="fieldHelp">
	  			<input name="module_param_{$key}" type="text" id="module_param_{$key}" value="{$param.defaultValue}" size="40" maxlength="240">
	  		</div>	
		  	<div class="fieldHelp">{tr}{$param.help}{/tr}</div>
		  </td>
		</tr>
	{/if}
  {/foreach}
{else}
<tr> 
  <td class="formcolor"><label for="params">{tr}Params{/tr}:</label></td>
  <td class="formcolor"><input name="params" type="text" id="params" value="{$module.params}" size="40" maxlength="240">(Eg. name=WikiPageName)</td>
</tr>
{/if}
<tr>
<td class="formcolor">&nbsp;</td>
<td class="formcolor">
<input type="hidden" name="moduleId" value="{$module.moduleId}" />
<input type="submit" name="assign" value="{tr}assign{/tr}" /></td>
</tr>
</table>
</form>
{/if}
