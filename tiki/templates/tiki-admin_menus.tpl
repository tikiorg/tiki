<a class="pagetitle" href="tiki-admin_menus.php">Admin Menus</a><br/>
<h2>{tr}Create/edit Menus{/tr}</h2>
<form action="tiki-admin_menus.php" method="post">
<input type="hidden" name="menuId" value="{$menuId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Type{/tr}:</td><td class="formcolor">
<select name="type">
<option value="d" {if $type eq 'd'}selected="selected"{/if}>{tr}dynamic colapsed{/tr}</option>
<option value="e" {if $type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr}</option>
<option velue="f" {if $type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr}</option>
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>Menus</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_menus.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'menuId_desc'}menuId_asc{else}menuId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading">{tr}options{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].menuId}</td>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].type}</td>
<td class="odd">{$channels[user].options}</td>
<td class="odd">
   <a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].menuId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}">{tr}options{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].menuId}</td>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].type}</td>
<td class="even">{$channels[user].options}</td>
<td class="even">
   <a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].menuId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_menus.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;menuId={$channels[user].menuId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-admin_menu_options.php?menuId={$channels[user].menuId}">{tr}options{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_menus.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_menus.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

