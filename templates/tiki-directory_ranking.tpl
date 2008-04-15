<h1><a class="pagetitle" href="tiki-directory_ranking.php?sort_mode={$sort_mode}">{tr}Directory ranking{/tr}</a></h1>
{* Display the title using parent *}
{include file=tiki-directory_bar.tpl}<br /><br />
{* Navigation bar to admin, admin related, etc *}

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<table class="normal">
  <tr>
    <td class="heading"><a class="tableheading" href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}Url{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}Country{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td class="{cycle advance=false}"><a class="link" href="tiki-directory_redirect.php?siteId={$items[user].siteId}" {if $prefs.directory_open_links eq 'n'}target='_blank'{/if}>{$items[user].name}</a></td>
<td class="{cycle advance=false}">{$items[user].url}</td>
{if $prefs.directory_country_flag eq 'y'}
<td class="{cycle advance=false}"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
{/if}
<td class="{cycle advance=false}">{$items[user].hits}</td>
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
<tr><td class="odd" colspan="4">{tr}No records{/tr}</td></tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
