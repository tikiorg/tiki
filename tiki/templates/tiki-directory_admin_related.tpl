<a class="pagetitle" href="tiki-directory_admin_related.php?parent={$parent}">{tr}Admin related categories{/tr}</a><br/><br/>
{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
{* Navigation bar to admin, admin related, etc *}

<h2>{tr}Parent category{/tr}:</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_related.php">
<select name="parent" onChange="javascript:path.submit();">
{section name=ix loop=$all_categs}
<option value="{$all_categs[ix].categId}" {if $parent eq $all_categs[ix].categId}selected="selected"{/if}>{$all_categs[ix].path}</option>
{/section}
</select>
<input type="submit" name="go" value="{tr}go{/tr}" />
</form>

<h2>Add a related category</h2>
<form action="tiki-directory_admin_related.php" method="post">
<input type="hidden" name="parent" value="{$parent}" />
<table class="normal">
  <tr>
    <td class="formcolor">{tr}Category{/tr}:</td>
    <td class="formcolor">
    <select name="categId" />
    {section name=ix loop=$categs}
      <option value="{$categs[ix].categId}">{$categs[ix].path}</option>
    {/section}
    </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Mutual{/tr}:</td>
    <td class="formcolor"><input type="checkbox" name="mutual" /></td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="add" value="{tr}save{/tr}" />
  </tr>
</table>
</form>

<h2>{tr}Related categories{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<table class="normal">
  <tr>
    <td class="heading">{tr}category{/tr}</td>
    <td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<form action="tiki-directory_admin_related.php">
<input type="hidden" name="parent" value="{$parent}" />
<input type="hidden" name="oldcategId" value="{$items[user].relatedTo}" />
<tr>
<td class="{cycle advance=false}">
<select name="categId">
{section name=ix loop=$categs}
      <option value="{$categs[ix].categId}" {if $categs[ix].categId eq $items[user].relatedTo}selected="selected"{/if}>{$categs[ix].path}</option>
{/section}
</select>
</td>
<td class="{cycle}">
<input type="submit" name="remove" value="{tr}remove{/tr}" />
<input type="submit" name="update" value="{tr}update{/tr}" />
</td>
</form>
</tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_admin_related.php?parent={$parent}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_admin_related.php?parent={$parent}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-directory_admin_related.php?parent={$parent}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
