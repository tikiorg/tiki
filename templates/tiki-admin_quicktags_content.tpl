
{* quicktags filter *}
<div align="center">
  <form action="tiki-admin_quicktags.php" method="get">
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
    <input type="hidden" name="tagId" value="{$tagId|escape}" />
    {tr}Quicktags category filter{/tr}
    <select name="category" onchange="this.form.submit()">
      <option value="All">{tr}All{/tr}</option>
      {section name=ct loop=$list_categories}
        <option value="{$list_categories[ct]}" {if $category eq $list_categories[ct]} selected="selected"{/if}>{tr}{$list_categories[ct]}{/tr}</option>
      {/section}
    </select>
  </form>
</div>

<br /><br />

<table class="normal">
  <tr>
    <td class="heading">
      <a class="tableheading" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?sort_mode={if $sort_mode eq 'taglabel_desc'}taglabel_asc{else}taglabel_desc{/if}{/ajax_href}>{tr}Label{/tr}
      </a>
    </td>
    <td class="heading">
      <a class="tableheading" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?sort_mode={if $sort_mode eq 'taginsert_desc'}taginsert_asc{else}taginsert_desc{/if}{/ajax_href}>{tr}Insert{/tr}
      </a>
    </td>
    <td class="heading">
      <a class="tableheading" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?sort_mode={if $sort_mode eq 'tagicon_desc'}tagicon_asc{else}tagicon_desc{/if}{/ajax_href}>{tr}Icon{/tr}
      </a>
    </td>
    <td class="heading">
      <a class="tableheading" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?sort_mode={if $sort_mode eq 'tagcategory_desc'}tagcategory_asc{else}tagcategory_desc{/if}{/ajax_href}>{tr}Category{/tr}
      </a>
    </td>
    <td class="heading">{tr}Action{/tr}</td>
  </tr>

{cycle values="odd,even" print=false}
{section name=tag loop=$quicktags}
  <tr>
    <td class="{cycle advance=false}">{$quicktags[tag].taglabel}</td>
    <td class="{cycle advance=false}">{$quicktags[tag].taginsert}</td>
    <td class="{cycle advance=false}">{html_image file=$quicktags[tag].tagicon} {$quicktags[tag].iconpath}</td>
    <td class="{cycle advance=false}">{tr}{$quicktags[tag].tagcategory}{/tr}</td>
    <td class="{cycle}">
      <a class="link" {ajax_href template="tiki-admin_quicktags_edit.tpl" htmlelement="quicktags-edit"}tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;tagId={$quicktags[tag].tagId}{/ajax_href}>
        <img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' />
      </a>
      &nbsp;&nbsp;
      <a class="link" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$quicktags[tag].tagId}">
        <img src='pics/icons/cross.png' alt="{tr}Remove{/tr}" border='0' width='16' height='16' />
      </a>
    </td>
  </tr>
{/section}
</table>

<div class="mini">
{if $prev_offset >= 0}
  [<a class="prevnext" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;category={$category}{/ajax_href}>{tr}Prev{/tr}
  </a>]
  &nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
  &nbsp;[<a class="prevnext" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;category={$category}{/ajax_href}>{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
  <br />
  {section loop=$cant_pages name=foo}
    {assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
      <a class="prevnext" {ajax_href template="tiki-admin_quicktags_content.tpl" htmlelement="quicktags-content"}tiki-admin_quicktags.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}&amp;category={$category}{/ajax_href}>
      {$smarty.section.foo.index_next}</a>&nbsp;
    {/section}
{/if}
</div>
