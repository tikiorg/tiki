{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-pagepermissions.tpl,v 1.23 2006-12-28 10:54:46 mose Exp $ *}

<h2>{tr}Assign permissions to page{/tr}: <a href="tiki-index.php?page={$page}">{$page}</a></h2>

<br />
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}These permissions override any global permissions affecting this object. To edit global permissions {/tr} <a class="rbox-link" href="tiki-admingroups.php">{tr}click here{/tr}</a>.</div>
</div>
<br />

<h3>{tr}Current permissions for this page{/tr}:</h3>
<table class="normal">
<tr><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td><td class="heading">{tr}action{/tr}</td></tr>
{cycle print=false values="even,odd"}
{section  name=pg loop=$page_perms}
<tr>
  <td class="{cycle advance=false}">{$page_perms[pg].groupName}</td>
  <td class="{cycle advance=false}">{$page_perms[pg].permName}</td>
  <td class="{cycle}">
    (<a class="link" href="tiki-pagepermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}">{tr}remove from this page{/tr}</a>)
    {if $inStructure eq "y"}(<a class="link" href="tiki-pagepermissions.php?referer={$referer}&amp;action=removestructure&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}">{tr}remove from this structure{/tr}</a>){/if}
  </td></tr>
{sectionelse}
<tr><td>{tr}No individual permissions; category or global permissions apply{/tr}</td></tr>
{/section}
</table>
<h3>{tr}Assign permissions{/tr}</h3>
<form method="post" action="tiki-pagepermissions.php">
{tr}assign{/tr}
<input type="hidden" name="page" value="{$page|escape}" />
<select name="perm">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName|escape}">{$perms[prm].permName}</option>
{/section}
</select>
{tr}to group{/tr}
<select name="group">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName|escape}">{$groups[grp].groupName}</option>
{/section}
</select>
{tr}for{/tr}
<input type="submit" name="assign" value="{tr}this page{/tr}" />
{if $inStructure eq "y"}<input type="submit" name="assignstructure" value="{tr}this structure{/tr}" />{/if}
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

<br /><br />
<h3>{tr}Current permissions for categories that this page belongs to{/tr}:</h3>
<table class="normal">
<tr><td class="heading">{tr}category{/tr}</td><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td></tr>
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
<tr><td>{tr}No category permissions; global permissions apply{/tr}</td></tr>
{/section}
</table>

<br /><br />

<span class="button2"><a href="tiki-index.php?page={$page}" class="linkbut">{tr}go back to{/tr} {$page}</a></span>
