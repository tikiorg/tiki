{* $Id$ *}
<h1><a class="pagetitle" href="tiki-directory_validate_sites.php">{tr}Validate sites{/tr}</a>
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Validate Sites{/tr}">
{icon _id='help'}</a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-directory_validate_sites.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}directory validate sites tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Tpl{/tr}'}</a>{/if}</h1>

{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
<br /><h2>{tr}Sites{/tr}</h2>

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_validate_sites.php" method="post" name="form_validate_sites">
<script type="text/javascript">
var CHECKBOX_LIST = [{section name=user loop=$items}'sites[{$items[user].siteId}]'{if not $smarty.section.user.last},{/if}{/section}];
</script>
<br />
<table class="normal">
  <tr>
    <th class="heading">{if $items}<input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_validate_sites',CHECKBOX_LIST,this.checked);" />{/if}</th>
    <th class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}Url{/tr}</a></th>
{if $prefs.directory_country_flag eq 'y'}
    <th class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}country{/tr}</a></th>
{/if}
    <th class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></th>
    <th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td  style="text-align:left;" class="{cycle advance=false}"><input type="checkbox" name="sites[{$items[user].siteId}]" /></td>
<td class="{cycle advance=false}">{$items[user].name}</td>
<td class="{cycle advance=false}"><a href="{$items[user].url}" target="_blank">{$items[user].url}</a></td>
{if $prefs.directory_country_flag eq 'y'}
<td class="{cycle advance=false}"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
{/if}
<td class="{cycle advance=false}">{$items[user].hits}</td>
<td  class="{cycle advance=false}">
   <a class="link" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
<tr>
  <td class="{cycle advance=false}">&nbsp;</td>
  <td class="{cycle}" colspan="5"><i>{tr}Categories{/tr}:{assign var=fsfs value=1}
  {section name=ii loop=$items[user].cats}
  {if $fsfs}{assign var=fsfs value=0}{else}, {/if}
  {$items[user].cats[ii].path}
  {/section}</i>
  </td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="6">{tr}No records found.{/tr}</td></tr>
{/section}
</table>
{if $items}
<br />{tr}Perform action with selected:{/tr} <input type="submit" name="del" value="{tr}Remove{/tr}" />
<input type="submit" name="validate" value="{tr}Validate{/tr}" />
{/if}
</form>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
