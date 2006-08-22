{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<h1>{tr}Assigned modules{/tr}</h1>
<h2>{tr}{$title}{/tr}</h2>

{*Create a new zone*}
{if $editZone}
	<div class="edubox" id="formCreateZone">
{else}
	<div class="edubox" id="formCreateZone" style="display:none;">
{/if}
<h2>{tr}Edit zone{/tr}</h2>
<form name="formCreateZone" method="post" action="tiki-workspaces_assigned_modules.php?wstypeId={$wstypeId}&wsmodtype={$wsmodtype}">
<input name="editZoneId" type="hidden" id="editZoneId" value="{$editZone.zoneId}">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      	<label for="zoneName">{tr}Name{/tr}:</label>
      </td>
      <td class="formcolor">
		<input name="zoneName" type="text" id="zoneName" value="{$editZone.name}" size="30" maxlength="100"/>
	  </td>
    </tr>
    <tr> 
      <td class="formcolor">
      	<label for="zoneDesc">{tr}Description{/tr}:</label>
      </td>
      <td class="formcolor">
      	<textarea name="zoneDesc" id="zoneDesc" size="30" cols="35" rows="3">{$editZone.description}</textarea></td>
      </td>
    </tr>
     <tr> 
      <td class="formcolor">
      	<label for="zoneOrder">{tr}Order{/tr}:</label>   
       </td>
      <td class="formcolor">   
	    <select id="zoneOrder" name="zoneOrder">
			{section name=ix loop=$orders}
			<option value="{$orders[ix]|escape}" {if $orders[ix]==$editZone.ord}selected{/if}>{$orders[ix]}</option>
			{/section}
		</select>
      </td>
	 </tr>
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="createZone" value="{tr}Save Zone{/tr}"/> <input class="edubutton" type="button" name="cancel" value="Cancel" onclick="document.getElementById('formCreateZone').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>


{* Add module to Zone *}
{if $canassign eq 'y'}
{if $module}
	<div id="formAddModule">
{else}
	<div id="formAddModule" style="display:none;">
{/if}	
<h2>{tr}Assign module{/tr}</h2>
<form name="moduleParamsForm" id="moduleParamsForm" action="tiki-workspaces_assigned_modules.php" method="post">
<input name="selectModule" id="selectModule" type="hidden" value=""/>
<input name="wstypeId" id="wstypeId" type="hidden" value="{$wstypeId}"/>
<input name="wsmodtype" id="wsmodtype" type="hidden" value="{$wsmodtype}"/>
<input name="zoneId" id="zoneId" type="hidden" value="{$activeZone.zoneId}"/>

<table class="normal">
<tr>
<td class="formcolor">
	<label for="module">{tr}Module{/tr}:</label>
</td>
<td class="formcolor">
	<select id="module" name="module" onchange="selectModule.value='y';moduleParamsForm.submit()">
	{section name=ix loop=$assignables}
	<option value="{$assignables[ix]|escape}" {if $assignables[ix]==$module.name}selected{/if}>{$assignables[ix]}</option>
	{/section}
	</select>
	{if $module.help}
		{ws_help}{$module.help}{/ws_help}
	{/if}
</td>
</tr>
<tr>
<td class="formcolor">
	<label for="position">{tr}Column{/tr}:</label></td>
<td class="formcolor">
	<select id="position" name="position">
		{foreach key=key item=column from=$modules}
			<option value="{$key}" {if "$key"==$module.position}selected{/if}>{$key}</option>
		{/foreach}
			<option value="{$max_columns}">{$max_columns}</option>
	</select>
</td>
</tr>
<tr>
<td class="formcolor">
	<label for="order">{tr}Order{/tr}:</label>
</td>
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
  			<input name="module_param_{$key}" type="text" id="module_param_{$key}" value="{$param.defaultValue}" size="40" maxlength="240">
		  	{ws_help}{$param.help}{/ws_help}
		  </td>
		</tr>
	{/if}
  {/foreach}
{else}
<tr> 
  <td class="formcolor">
  	<label for="params">{tr}Params{/tr}:</label></td>
  <td class="formcolor">
  	<input name="params" type="text" id="params" value="{$module.params}" size="40" maxlength="240">
  </td>
</tr>
{/if}
<tr>
<td class="formcolor" colspan="2">
	<input type="hidden" name="moduleId" value="{$module.moduleId}" />
	<center><input class="edubutton" type="submit" name="assign" value="{tr}Assign module{/tr}"/> <input class="edubutton" type="button" name="cancel" value="Cancel" onclick="document.getElementById('formAddModule').style.display = 'none';"></center></td>
</td>
</tr>
</table>
</form>
</div>
{/if}



{* Buttons *}
<div class="edubuttons">
{if $activeZone}
	<a class="edubutton" href="tiki-workspaces_assigned_modules.php?&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}&removeZone={$activeZone.zoneId}">
	<img border='0'src='images/workspaces/edu_zones_remove.png' alt='remove zone'/>{tr}Remove zone{/tr}</a>
	<a class="edubutton" href="tiki-workspaces_assigned_modules.php?wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}&editZone={$activeZone.zoneId}" >
	<img border='0'src='images/workspaces/edu_zones_edit.png' alt='edit zone'/> {tr}Edit zone{/tr}</a>
	<a class="edubutton" href="#" onclick="document.getElementById('formAddModule').style.display = 'block';">
	<img border='0'src='images/workspaces/module_add.png'/> {tr}Assign module{/tr}</a>
{/if}
	<a class="edubutton" href="#" onclick="document.getElementById('formCreateZone').style.display = 'block';">
	<img border='0'src='images/workspaces/edu_zones_add.png' alt='create zone'/> {tr}Create zone{/tr}</a>
</div>

<br/>
<h2>{tr}Desktop{/tr}</h2>
<br/>

{if $zones}
	<div class="zoneTabs">
	{foreach key=key item=zone from=$zones}
		{if $activeZone.zoneId == $key}
			<div class="activetabbut"><img src='images/workspaces/redarrow.gif' align="bottom" border='0'/><a href="tiki-workspaces_assigned_modules.php?wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$key}" class="activetablink"> {tr}{$zone.name}{/tr} </a></div>
		{else}
			<div class="noactivetabbut"><img src='images/workspaces/grayarrow.gif' align="bottom" border='0'/><a href="tiki-workspaces_assigned_modules.php?wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$key}" class="noactivetablink"> {tr}{$zone.name}{/tr} </a></div>
		{/if}
	{/foreach}
	</div>
{else}
{tr}No zones defined, please create a first zone to start{/tr}.
{/if}


{* Modules *}
<div class="workspace_desktop">
{if $modules}
	<table width="100%">
	<tr>
	{foreach key=key item=column from=$modules}
		<td >
		<h5>{tr}Column{/tr} {$key}</h5>
		</td>
	{/foreach}
	</tr>
	<tr>
	{foreach key=key item=column from=$modules}
		<td >
			<table  class="normal">
				{cycle values="odd,even" print=false}
				{section name=ix loop=$modules[$key]}
				<tr>
					<td class="{cycle advance=false}">
						<div class="wsbox-title">
						{$modules[$key][ix].ord} - {$modules[$key][ix].title}
						</div>
						<div class="wsbox-data">
						  {$modules[$key][ix].name}<br/>
		  				  <a class="link" href="tiki-workspaces_assigned_modules.php?left={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}"><img src='img/icons2/nav_dot_right.gif' alt='{tr}left{/tr}' title='{tr}move to left column{/tr}' border='0' /></a>
					  	  <a class="link" href="tiki-workspaces_assigned_modules.php?up={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}"><img src='img/icons2/up.gif' alt='{tr}up{/tr}' title='{tr}up{/tr}' border='0' /></a>
	  				      <a class="link" href="tiki-workspaces_assigned_modules.php?down={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}"><img src='img/icons2/down.gif' alt='{tr}down{/tr}' title='{tr}down{/tr}' border='0' /></a>
	  				      <a class="link" href="tiki-workspaces_assigned_modules.php?right={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}"><img src='img/icons2/nav_dot_left.gif' alt='{tr}right{/tr}' title='{tr}move to right column{/tr}' border='0' /></a>
					      <a class="link" href="tiki-workspaces_assigned_modules.php?unassign={$modules[$key][ix].name}&moduleId={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}"><img src='images/workspaces/module_remove.png' border='0' alt='{tr}unassign{/tr}' title='{tr}unassign{/tr}' /></a> 
	  				      <a class="link" href="tiki-workspaces_assigned_modules.php?edit={$modules[$key][ix].moduleId}&wstypeId={$wstypeId}&wsmodtype={$wsmodtype}&zoneId={$activeZone.zoneId}">
			              <img src='images/workspaces/module_edit.png' border='0' alt='Edit' title='Edit Module' /></a>			
			             </div>
	  				  				
					</td>
				</tr>
				{/section}
			</table>
		</td>
	{/foreach}	
	</tr>
	</table>
{else}
{tr}No modules assigned to the zone, please assign a first module{/tr}.
{/if}
</div>

