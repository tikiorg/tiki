{if $prefs.feature_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
{if $notable neq 'y'}
<tr class="formcolor">
 <td>{tr}Categorize{/tr}</td>
 <td{if $colsCategorize} colspan="{$colsCategorize}"{/if}>
 {/if}
{if $mandatory_category >= 0}
  <div id="categorizator">
{else}
<a class="link" href="javascript:flip_multi('categorizator');flip_multi('categshow','inline');flip_multi('categhide','inline');"{if ($mid eq 'tiki-editpage.tpl')}onclick="needToConfirm=false;"{/if}>
{if $prefs.javascript_enabled eq 'y'}
<span id="categshow" name="categshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}none{else}inline{/if};">{tr}Show Categories{/tr}</span>
<span id="categhide" name="categhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}inline{else}none{/if};">{tr}Hide Categories{/tr}</span>
{/if}
</a>
  <div id="categorizator" name="categorizator" style="display:{if (isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y') or $prefs.javascript_enabled ne 'y'}block{else}none{/if};">
{/if}
  {if count($categories) gt 0}
    <div class="multiselect">
      {cycle values="odd,even" print=false}
      {section name=ix loop=$categories}
      {if $categories[ix].incat eq 'y'}
				<div class="{cycle}" style="display: inline"><input type="checkbox" name="cat_categories[]" value="{$categories[ix].categId|escape}" checked="checked"/>{if $categories[ix].categpath}{$categories[ix].categpath}{else}{$categories[ix].name}{/if}</div>
			{/if}
      {/section}
      {section name=ix loop=$categories}
      {if $categories[ix].incat neq 'y'}
				<div class="{cycle}" style="display: inline"><input type="checkbox" name="cat_categories[]" value="{$categories[ix].categId|escape}"/>{if $categories[ix].categpath}{$categories[ix].categpath}{else}{$categories[ix].name}{/if}</div>
			{/if}
      {/section}
    </div>
    <input type="hidden" name="cat_categorize" value="on" />
		<input type="checkbox" name="cat_clearall" value="on" />{tr}Clear all Categories{/tr}<br/>
  {else}
    {tr}No categories defined{/tr} <br />
  {/if}
  {if $tiki_p_admin_categories eq 'y'}
    <a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr}</a>
  {/if}
  </div>
	{if $notable neq 'y'}
  </td>
</tr>
  {/if}
{/if}

