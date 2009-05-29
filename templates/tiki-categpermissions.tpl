{* $Id$  *}

<h1>{tr}Assign permissions to category{/tr}: &nbsp;<a href="tiki-admin_categories.php?parentId=0">{tr}Top{/tr}</a>
{section name=x loop=$path}
::
<a href="tiki-admin_categories.php?parentId={$path[x].categId}">{$path[x].name}</a>
{/section}</h1>

<div class="navbar">
	{button href="tiki-admin_categories.php" _text="{tr}Admin Categories{/tr}"}
</div>

{if $prefs.feature_search_show_forbidden_cat eq 'y'}
	<div class="simplebox highlight">{tr}Categories checking is not done in the database search.{/tr}</div>
{/if}

<br />

<h2>{tr}Current permissions for this category{/tr}:</h2>
<form name="checkboxes_on" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="categId" value="{$catId|escape}" />
<table class="normal">
<tr>
	<th>
		{select_all checkbox_names='checked[]'"}
	</th>
	<th>{tr}Group{/tr}</th>
	<th>{tr}Permission{/tr}</th>
	<th>{tr}Action{/tr}</th>
</tr>
{cycle print=false values="even,odd"}
{section  name=pg loop=$category_perms}
<tr>
  <td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$catId} {$category_perms[pg].permName} {$category_perms[pg].groupName|escape}" /></td>
  <td class="{cycle advance=false}">{$category_perms[pg].groupName}</td>
  <td class="{cycle advance=false}">{$category_perms[pg].permName}</td>
  <td class="{cycle}">
    <a class="link" href="tiki-categpermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;categId={$catId}&amp;perm={$category_perms[pg].permName}&amp;group={$category_perms[pg].groupName}" title="{tr}Remove from this Category Only{/tr}">{icon _id=cross alt="{tr}Remove from this Category Only{/tr}" style="vertical-align:middle"}</a>
	(<a class="link" href="tiki-categpermissions.php?referer={$referer}&amp;action=remove_all&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;categId={$catId}&amp;perm={$category_perms[pg].permName}&amp;group={$category_perms[pg].groupName}">{tr}Remove from this Category &amp; all its Children{/tr}</a>)
  </td></tr>
{sectionelse}
<tr><td class="odd" colspan="4">{tr}No individual permissions. Global permissions apply.{/tr}</td></tr>
{/section}
</table>
{tr}Perform action with checked{/tr}:
<select name="submit_mult" onchange="this.form.submit();">
	<option value="" selected="selected">...</option>
	<option value="remove" >{tr}Remove{/tr}</option>
</select>
<script type='text/javascript'>
<!--
  // Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
  //-->
</script>
   
<noscript>
	<input type="submit" value="{tr}OK{/tr}" />
</noscript>
</form>
<br />

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
<div class="simplebox highlight">{icon _id=information style="vertical-align:middle"} {tr}Assigning permissions for <b>all children</b> is recommended for best performance.{/tr}</div>
