{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-directory_validate_sites.tpl,v 1.22 2007-07-17 16:21:49 jyhem Exp $ *}
<h1><a class="pagetitle" href="tiki-directory_validate_sites.php">{tr}Validate sites{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Validate Sites{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-directory_validate_sites.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}directory validate sites tpl{/tr}">
<img src="'pics/icons/shape_square_edit.png" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
<h2>{tr}Sites{/tr}</h2>

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_validate_sites.php" method="post" name="form_validate_sites">
<input type="submit" name="del" value="{tr}remove{/tr}" />
<input type="submit" name="validate" value="{tr}validate{/tr}" />
<script type="text/javascript">
var CHECKBOX_LIST = [{section name=user loop=$items}'sites[{$items[user].siteId}]'{if not $smarty.section.user.last},{/if}{/section}];
</script>
<table class="normal">
  <tr>
    <td class="heading"><input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_validate_sites',CHECKBOX_LIST,this.checked);" /></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}url{/tr}</a></td>
{if $directory_country_flag eq 'y'}
    <td class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}country{/tr}</a></td>
{/if}
    <td class="heading"><a class="tableheading" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}hits{/tr}</a></td>
    <td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td  style="text-align:left;" class="{cycle advance=false}"><input type="checkbox" name="sites[{$items[user].siteId}]" /></td>
<td class="{cycle advance=false}">{$items[user].name}</td>
<td class="{cycle advance=false}"><a href="{$items[user].url}" target="_blank">{$items[user].url}</a></td>
{if $directory_country_flag eq 'y'}
<td class="{cycle advance=false}"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
{/if}
<td class="{cycle advance=false}">{$items[user].hits}</td>
<td  class="{cycle advance=false}">
   <a class="link" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}"><img src='pics/icons/page_edit.png'  height="16" width="16" border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
   <a class="link" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}"><img src='pics/icons/cross.png' border='0' height="16" width="16" alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
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
[<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-directory_validate_sites.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
