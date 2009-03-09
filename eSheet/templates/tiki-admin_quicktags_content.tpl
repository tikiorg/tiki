
{* quicktags filter *}
{if $list_categories|@count gt 1}
<div align="center">
  <form action="tiki-admin_quicktags.php" method="get">
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
    <input type="hidden" name="tagId" value="{$tagId|escape}" />
    {tr}Quicktags category filter{/tr}
    <select name="category" onchange="this.form.submit()">
      <option value="All">{tr}All{/tr}</option>
      {foreach item=text key=value from=$list_categories}
        <option value="{$value}"{if $category eq $value} selected="selected"{/if}>{$text}</option>
      {/foreach}
    </select>
  </form>
</div>
{/if}

<br /><br />

<table class="normal">
  <tr>
    <th>&nbsp;</th>
    {foreach from=$table_headers item=header key=sort_field}
    <th>{self_link _template='tiki-admin_quicktags_content.tpl' _htmlelement='quicktags-content' _sort_field=$sort_field _sort_arg='sort_mode'}{$header}{/self_link}</th>
    {/foreach}
    <th>{tr}Action{/tr}</th>
  </tr>

{cycle values="odd,even" print=false}
{section name=tag loop=$quicktags}
  <tr>
    <td class="{cycle advance=false}">{icon _id=$quicktags[tag].tagicon}</td>
    {foreach from=$table_headers item=header key=sort_field}
    <td class="{cycle advance=false}">
      {if $sort_field eq 'tagcategory'}
         {assign var=qtkey value=$quicktags[tag].$sort_field}
         {$list_categories[$qtkey]}
      {else}
         {tr}{$quicktags[tag].$sort_field}{/tr}
      {/if}
    </td>
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
