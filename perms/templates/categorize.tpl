{if $prefs.feature_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
{if $notable neq 'y'}
<tr class="formcolor">
 <td>{tr}Categorize{/tr}</td>
 <td{if $colsCategorize} colspan="{$colsCategorize}"{/if}>
{/if}
{if $mandatory_category >= 0 or $prefs.javascript_enabled neq 'y'}
  <div id="categorizator">
{else}
{button href="#" _flip_id='categorizator' _class='link' _text='{tr}Select Categories{/tr}' _flip_default_open='n'}
  <div id="categorizator" name="categorizator" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}block{else}none{/if};">
{/if}
  <div class="multiselect">
  {if count($categories) gt 0}
{strip}
	{if isset($cat_tree) }
		{$cat_tree}
	{else}
    <div id="categories_select" {*onmouseover="show('categories_select');" onmouseout="hide('categories_select');"*} class="selection">
      {cycle values="odd,even" print=false}
      {section name=ix loop=$categories}
      {if $categories[ix].incat eq 'y'}
				<div class="{cycle} option"><input type="checkbox" name="cat_categories[]" value="{$categories[ix].categId|escape}" checked="checked"/>{if $categories[ix].categpath}{$categories[ix].categpath}{else}{$categories[ix].name}{/if}</div>
			{/if}
      {/section}
      {section name=ix loop=$categories}
      {if $categories[ix].incat neq 'y'}
				<div class="{cycle} option"><input type="checkbox" name="cat_categories[]" value="{$categories[ix].categId|escape}"/>{if $categories[ix].categpath}{$categories[ix].categpath}{else}{$categories[ix].name}{/if}</div>
			{/if}
      {/section}
    </div>
	 {/if}
{/strip}
    <input type="hidden" name="cat_categorize" value="on" />
	<div class="clear">
	{if $tiki_p_admin_categories eq 'y'}
    	<div class="floatright"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon _id='wrench'}</a></div>
	{/if}
	
	{select_all checkbox_names='cat_categories[]' label="{tr}Select/deselect all categories{/tr}"}
  
	{else}
	<div class="clear">
 	{if $tiki_p_admin_categories eq 'y'}
    <div class="floatright"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon _id='wrench'}</a></div>
 	{/if}
    {tr}No categories defined{/tr}
  {/if}
    </div> {* end .clear *}
   </div> {* end #multiselect *}
  </div> {* end #categorizator *}
	{if $notable neq 'y'}
  </td>
</tr>
  {/if}
{/if}

