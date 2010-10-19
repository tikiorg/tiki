{* $Id$ *}
{title help="Directory"}{tr}Validate sites{/tr}{/title}

{* Display the title using parent *}
{include file='tiki-directory_admin_bar.tpl'} <br />
<h2>{tr}Sites{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_validate_sites.php" method="post" name="form_validate_sites">
{jq notonready=true}
var CHECKBOX_LIST = [{{section name=user loop=$items}'sites[{$items[user].siteId}]'{if not $smarty.section.user.last},{/if}{/section}}];
{/jq}
  <br />
  <table class="normal">
    <tr>
      <th>{if $items}
        <input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_validate_sites',CHECKBOX_LIST,this.checked);" />
        {/if}</th>
      <th><a href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
      <th><a href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}Url{/tr}</a></th>
      {if $prefs.directory_country_flag eq 'y'}
      <th><a href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}country{/tr}</a></th>
      {/if}
      <th><a href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></th>
      <th>{tr}Action{/tr}</th>
    </tr>
    {cycle values="odd,even" print=false}
    {section name=user loop=$items}
    <tr class="{cycle advance=false}">
      <td style="text-align:left;"><input type="checkbox" name="sites[{$items[user].siteId}]" /></td>
      <td>{$items[user].name}</td>
      <td><a href="{$items[user].url}" target="_blank">{$items[user].url}</a></td>
      {if $prefs.directory_country_flag eq 'y'}
      <td><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
      {/if}
      <td>{$items[user].hits}</td>
      <td><a class="link" href="tiki-directory_admin_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}">{icon _id='page_edit'}</a> <a class="link" href="tiki-directory_validate_sites.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a> </td>
    </tr>
    <tr class="{cycle}">
      <td>&nbsp;</td>
      <td colspan="5"><i>{tr}Directory Categories{/tr}:{assign var=fsfs value=1}
        {section name=ii loop=$items[user].cats}
        {if $fsfs}{assign var=fsfs value=0}{else}, {/if}
        {$items[user].cats[ii].path}
        {/section}</i> </td>
    </tr>
    {sectionelse}
    <tr>
      <td class="odd" colspan="6">{tr}No records found.{/tr}</td>
    </tr>
    {/section}
  </table>
  {if $items} <br />
  {tr}Perform action with selected:{/tr}
  <input type="submit" name="del" value="{tr}Remove{/tr}" />
  <input type="submit" name="validate" value="{tr}Validate{/tr}" />
  {/if}
</form>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links} 
