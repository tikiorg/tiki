<a class="pagetitle" href="tiki-directory_admin_related.php?parent={$parent}">{tr}Admin Related Categories{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=DirectoryDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Directory Related {/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-directory_admin_related.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}directory admin related tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->

<br /><br />
{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
{* Navigation bar to admin, admin related, etc *}

<h2>{tr}Parent category{/tr}:</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_related.php">
{tr}Search: {/tr} <select name="parent" onchange="javascript:path.submit();">
{section name=ix loop=$all_categs}
<option value="{$all_categs[ix].categId|escape}" {if $parent eq $all_categs[ix].categId}selected="selected"{/if}>{$all_categs[ix].path}</option>
{/section}
</select>
<input type="submit" name="go" value="{tr}Go{/tr}" />
</form>

<h2>{tr}Add a related category{/tr}</h2>
<form action="tiki-directory_admin_related.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
<table class="normal">
  <tr>
    <td class="formcolor">{tr}Category{/tr}:</td>
    <td class="formcolor">
    <select name="categId">
    {section name=ix loop=$categs}
      <option value="{$categs[ix].categId|escape}">{$categs[ix].path}</option>
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
    <td class="formcolor"><input type="submit" name="add" value="{tr}Save{/tr}" />
  </tr>
</table>
</form>

<h2>{tr}Related Categories{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<table class="normal">
  <tr>
    <td class="heading">{tr}Category{/tr}</td>
    <td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<form action="tiki-directory_admin_related.php">
<input type="hidden" name="parent" value="{$parent|escape}" />
<input type="hidden" name="oldcategId" value="{$items[user].relatedTo|escape}" />
<tr>
<td class="{cycle advance=false}">
<select name="categId">
{section name=ix loop=$categs}
      <option value="{$categs[ix].categId|escape}" {if $categs[ix].categId eq $items[user].relatedTo}selected="selected"{/if}>{$categs[ix].path}</option>
{/section}
</select>
</td>
<td class="{cycle}">
<input type="submit" name="remove" value="{tr}Remove{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this relationship?{/tr}')"/>
<input type="submit" name="update" value="{tr}Update{/tr}" />
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
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-directory_admin_related.php?parent={$parent}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
