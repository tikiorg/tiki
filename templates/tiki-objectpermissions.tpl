{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-objectpermissions.tpl,v 1.32.2.2 2008-03-11 15:17:55 nyloth Exp $ *}
<h1><a href="tiki-objectpermissions.php?objectName={$objectName|escape:url}&amp;objectType={$objectType|escape:url}&amp;objectId={$objectId|escape:url}&amp;permType={$permType|escape:url}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{tr}Assign permissions to {/tr}{tr}{$objectType|escape}{/tr}: {$objectName|escape}</a></h1>
<div class="navbar">
<a href="{$referer}" class="linkbut">{tr}Back{/tr}</a>
</div>

<h2>{tr}Current permissions for this object{/tr}</h2>
{if $filegals_manager ne 'y'}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
{if $tiki_p_admin eq 'y'}{tr}To edit global permissions <a class="rbox-link" href="tiki-admingroups.php">click here</a>.{/tr}{/if}
</div>
</div>
{/if}

<form method="post" action="tiki-objectpermissions.php{if $filegals_manager eq 'y'}?filegals_manager=y{/if}">
<table class="normal">
<tr><td class="heading"></td><td class="heading">{tr}Group{/tr}</td><td class="heading">{tr}Permission{/tr}</td><td class="heading">{tr}Action{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section  name=pg loop=$page_perms}
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$page_perms[pg].permName|cat:' '|cat:$page_perms[pg].groupName|escape}"  {if $smarty.request.checked and in_array($page_perms[pg].permName.' '.$page_perms[pg].groupName,$smarty.request.checked)}checked="checked"{/if} />
<td class="{cycle advance=false}">{$page_perms[pg].groupName}</td>
<td class="{cycle advance=false}">{$page_perms[pg].permName}</td>
<td class="{cycle advance=true}"><a class="link" href="tiki-objectpermissions.php?referer={$referer|escape:"url"}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page|escape:"url"}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a></td></tr>
{sectionelse}
<tr><td colspan="4" class="odd">{if !empty($categ_perms)}{tr}No individual permissions, category permissions apply{/tr}{else}{tr}No individual permissions, category permissions apply{/tr}{/if}</td></tr>
{/section}
{if $page_perms}
	<tr><td>
	<script type="text/javascript"> /* <![CDATA[ */
	document.write('<tr><td colspan="4"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'checked[]\',this.checked)"/>');
	document.write('<label for="clickall">{tr}Select All{/tr}</label></td></tr>');
	/* ]]> */</script>
	</td></tr>
{/if}
</table>
<div>
{tr}Perform action with checked:{/tr} 
<input type="image" name="delsel" src='pics/icons/cross.png' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' />
{if isset($inStructure)}
{tr}and also to all pages of the sub-structure:{/tr} <input name="removestructure" type="checkbox" />
{/if}
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
<input type="submit" name="assign" value="{tr}Assign{/tr}" />

{assign var=nbp value=$perms|@count}
<select name="perm[]" multiple="multiple" size="{$nbp}">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName|escape}">{$perms[prm].permName|escape}</option>
{/section}
</select>
{tr}to group{/tr}:
<select name="group[]" multiple="multiple" size="{if $nbp <=1}5{else}{$nbp}{/if}">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName|escape}</option>
{/section}
</select>
<input type="submit" name="assign" value="{tr}Assign{/tr}" />
{if ($objectType eq 'wiki' or $objectType eq 'wiki page') and !empty($inStructure)}
{tr}and also to all pages of the sub-structure:{/tr} <input name="assignstructure" type="checkbox" />
{/if}
<br /><br />
<div class="button2">
<a href="#" onclick="javascript:flip('edithelpzone'); return false;" class="linkbut">{tr}Perms Help{/tr}</a>
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

<h2>{tr}Current permissions for categories that this object belongs to{/tr}:</h2>
{if !empty($page_perms) && !empty($categ_perms)}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}These permissions do not apply. Special permissions apply.{/tr}
</div>
{/if}<table class="normal">
<tr><td class="heading">{tr}Category{/tr}</td><td class="heading">{tr}Group{/tr}</td><td class="heading">{tr}Permission{/tr}</td></tr>
{cycle print=false values="even,odd"}
{section  name=x loop=$categ_perms}
	{section name=y loop=$categ_perms[x]}
<tr>
  <td class="{cycle advance=false}">{$categ_perms[x][0].catpath}</td>
  <td class="{cycle advance=false}">{$categ_perms[x][y].groupName}</td>
  <td class="{cycle advance=false}">{$categ_perms[x][y].permName}</td>
</tr>
	{/section}
{sectionelse}
<tr><td colspan="3">{if empty($page_perms)}{tr}No category permissions; global permissions apply{/tr}{else}{tr}No category permissions; special permissions apply{/tr}{/if}</td></tr>
{/section}
</table>
