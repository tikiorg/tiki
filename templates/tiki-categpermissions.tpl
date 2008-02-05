{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-categpermissions.tpl,v 1.13.2.1 2008-02-05 16:14:44 jyhem Exp $  *}

<h1>{tr}Assign permissions to category{/tr}: &nbsp;<a href="tiki-admin_categories.php?parentId=0">{tr}Top{/tr}</a>
{section name=x loop=$path}
::
<a href="tiki-admin_categories.php?parentId={$path[x].categId}">{$path[x].name}</a>
{/section}</h1>
<div class="navbar"><a class="linkbut" href="tiki-admin_categories.php">{tr}Admin Categories{/tr}</a></div>
{if $prefs.feature_search_show_forbidden_cat eq 'y'}
<div class="simplebox highlight">{tr}Categories checking is not done in the database search.{/tr}</div>
{/if}

<h2>{tr}Current permissions for this category{/tr}:</h2>
<table class="normal">
<tr><td class="heading">{tr}Group{/tr}</td><td class="heading">{tr}Permission{/tr}</td><td class="heading">{tr}Action{/tr}</td></tr>
{cycle print=false values="even,odd"}
{section  name=pg loop=$category_perms}
<tr>
  <td class="{cycle advance=false}">{$category_perms[pg].groupName}</td>
  <td class="{cycle advance=false}">{$category_perms[pg].permName}</td>
  <td class="{cycle}">
    (<a class="link" href="tiki-categpermissions.php?referer={$referer}&amp;action=remove_all&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;categId={$catId}&amp;perm={$category_perms[pg].permName}&amp;group={$category_perms[pg].groupName}">{tr}Remove from this Category &amp; all its Children{/tr}</a>)
    (<a class="link" href="tiki-categpermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;categId={$catId}&amp;perm={$category_perms[pg].permName}&amp;group={$category_perms[pg].groupName}">{tr}Remove from this Category Only{/tr}</a>)
  </td></tr>
{sectionelse}
<tr><td>{tr}No individual permissions global permissions apply{/tr}</td></tr>
{/section}
</table>
<h2>{tr}Assign permissions{/tr}</h2>
<form method="post" action="tiki-categpermissions.php" class="form">
{tr}Assign{/tr}
<input type="hidden" name="categId" value="{$catId|escape}" />
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
<input type="submit" name="assign_all" value="{tr}this category &amp; all its children{/tr}" />
{tr}or{/tr}
<input type="submit" name="assign" value="{tr}this category only{/tr}" />
</form>
<div class="simplebox highlight">{tr}Assigning permissions for <b>all children</b> is recommended for best performance.{/tr}</div>