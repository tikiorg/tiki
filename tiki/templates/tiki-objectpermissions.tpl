{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-objectpermissions.tpl,v 1.19 2006-12-01 18:15:47 sylvieg Exp $ *}
<h1>{tr}Assign permissions to {/tr}{tr}{$objectType|escape}{/tr} {$objectName|escape}</h1>
<a href="{$referer}" class="linkbut">{tr}back{/tr}</a>
<div>
<h2>{tr}Current permissions for this object{/tr}</h2>
<form method="post" action="tiki-objectpermissions.php">
<table class="normal">
<tr><td class="heading"></td><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td><td class="heading">{tr}action{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section  name=pg loop=$page_perms}
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$page_perms[pg].permName|cat:' '|cat:$page_perms[pg].groupName|escape}"  {if $smarty.request.checked and in_array($page_perms[pg].permName.' '.$page_perms[pg].groupName,$smarty.request.checked)}checked="checked"{/if} />
<td class="{cycle advance=false}">{$page_perms[pg].groupName}</td>
<td class="{cycle advance=false}">{$page_perms[pg].permName}</td>
<td class="{cycle advance=true}"><a class="link" href="tiki-objectpermissions.php?referer={$referer|escape:"url"}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page|escape:"url"}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}" title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" width="16" height="16" alt="{tr}delete{/tr}" border="0" /></a></td></tr>
{sectionelse}
<tr><td colspan="4" class="odd">{tr}No individual permissions global permissions apply{/tr}</td></tr>
{/section}
{if $page_perms}
	<tr><td>
	<script type="text/javascript"> /* <![CDATA[ */
	document.write('<tr><td colspan="4"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'checked[]\',this.checked)"/>');
	document.write('<label for="clickall">{tr}select all{/tr}</label></td></tr>');
	/* ]]> */</script>
	</td></tr>
{/if}
</table>
<div>
{tr}Perform action with checked:{/tr} 
<input type="image" name="delsel" src='pics/icons/cross.png' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' />
</div>

<h2>{tr}Assign permissions to this object{/tr}</h2>
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}Hold down CTRL to select multiple{/tr}</div>
</div>
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="referer" value="{$referer|escape}" />
<input type="hidden" name="objectName" value="{$objectName|escape}" />
<input type="hidden" name="objectType" value="{$objectType|escape}" />
<input type="hidden" name="objectId" value="{$objectId|escape}" />
<input type="hidden" name="permType" value="{$permType|escape}" />
<input type="submit" name="assign" value="{tr}assign{/tr}" />

<select name="perm[]" multiple="multiple" size="{$perms|@count}">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName|escape}">{$perms[prm].permName|escape}</option>
{/section}
</select>
{tr}to group{/tr}:
<select name="group[]" multiple="multiple" size="{$perms|@count}">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName|escape}</option>
{/section}
</select>
<input type="submit" name="assign" value="{tr}assign{/tr}" />
<br /><br />
<div class="button2">
<a href="#" onclick="javascript:flip('edithelpzone'); return false;" class="linkbut">{tr}perms help{/tr}</a>
</div>
<div class="wiki-edithelp"  id='edithelpzone' >
{cycle print=false values="even,odd"}
<table class="normal">
{section name=prm loop=$perms}
<tr><td class="{cycle advance=false}">{$perms[prm].permName}</td><td class="{cycle}">{tr}{$perms[prm].permDesc}{/tr}</tr>
{/section}
</table>
</div>
</form>
</div>
