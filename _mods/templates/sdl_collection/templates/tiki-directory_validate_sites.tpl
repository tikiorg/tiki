<a class="pagetitle" href="tiki-directory_validate_sites.php">{tr}Validate Sites{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=DirectoryDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Validate Sites{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-directory_validate_sites.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}directory validate sites tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->

<br/><br/>
{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
<h2>{tr}Sites{/tr}</h2>

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_validate_sites.php" method="post">
<input type="submit" name="del" value="{tr}Remove{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this site?{/tr}')"/>
<input type="submit" name="validate" value="{tr}Validate{/tr}" />
<table class="normal">
  <tr>
    <td class="heading">&nbsp;</td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}Country{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
    <td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td  style="text-align:center;" class="{cycle advance=false}"><input type="checkbox" name="sites[{$items[user].siteId}]" /></td>
<td class="{cycle advance=false}">{$items[user].name}</td>
<td class="{cycle advance=false}">{$items[user].url}</td>
<td class="{cycle advance=false}"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
<td class="{cycle advance=false}">{$items[user].hits}</td>
<td  class="{cycle advance=false}">
   <a class="link" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
   <a class="link" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}"><img src='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
</td>
</tr>
<tr>
  <td class="{cycle advance=false}">&nbsp;</td>
  <td class="{cycle}" colspan="5"><i>{tr}categories{/tr}:{assign var=fsfs value=1}
  {section name=ii loop=$items[user].cats}
  {if $fsfs}{assign var=fsfs value=0}{else}, {/if}
  {$items[user].cats[ii].path}
  {/section}</i>
  </td>
</tr>
{/section}
</table>
</form>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_admin_sites.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_admin_sites.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-directory_admin_sites.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>