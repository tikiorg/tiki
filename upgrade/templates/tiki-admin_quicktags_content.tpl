
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
    <td class="heading">&nbsp;</td>
    {foreach from=$table_headers item=header key=sort_field}
    <td class="heading">{self_link _template='tiki-admin_quicktags_content.tpl' _htmlelement='quicktags-content' _sort_field=$sort_field _sort_arg='sort_mode' _class='tableheading'}{$header}{/self_link}</td>
    {/foreach}
    <td class="heading">{tr}Action{/tr}</td>
  </tr>

{cycle values="odd,even" print=false}
{section name=tag loop=$quicktags}
  <tr>
    <td class="{cycle advance=false}">{icon _id=$quicktags[tag].tagicon}</td>
    {foreach from=$table_headers item=header key=sort_field}
    <td class="{cycle advance=false}">{tr}{$quicktags[tag].$sort_field}{/tr}</td>
    {/foreach}
    <td class="{cycle}">
      {self_link _template='tiki-admin_quicktags_edit.tpl' _htmlelement='quicktags-edit' _class='link' tagId=$quicktags[tag].tagId _icon='page_edit'}{tr}Edit{/tr}{/self_link}
      &nbsp;&nbsp;
      {self_link _class='link' remove=$quicktags[tag].tagId _ajax='n' _icon='cross'}{tr}Remove{/tr}{/self_link}
    </td>
  </tr>
{/section}
</table>

{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset template='tiki-admin_quicktags_content.tpl' htmlelement='quicktags-content'}{/pagination_links}
