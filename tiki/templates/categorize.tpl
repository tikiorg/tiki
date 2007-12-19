{if $prefs.feature_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
<tr class="formcolor">
 <td>{tr}Categorize{/tr}</td>
 <td{if $colsCategorize} colspan="{$colsCategorize}"{/if}>
{if $mandatory_category >= 0}
  <div id="categorizator">
{else}
<a class="link" href="javascript:flip('categorizator');flip('categshow','inline');flip('categhide','inline');"{if ($mid eq 'tiki-editpage.tpl')}onclick="needToConfirm=false;"{/if}>
<span id="categshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}none{else}inline{/if};">{tr}Show Categories{/tr}</span>
<span id="categhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}inline{else}none{/if};">{tr}hide categories{/tr}</span>
</a>
  <div id="categorizator" style="display:{if isset($smarty.session.tiki_cookie_jar.show_categorizator) and $smarty.session.tiki_cookie_jar.show_categorizator eq 'y'}block{else}none{/if};">
{/if}
{if $prefs.feature_help eq 'y'}
  <div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}Hold down CTRL to select multiple categories{/tr}.</div>
  </div>
  <br />
{/if}
  {if count($categories) gt 0}
   <select name="cat_categories[]" multiple="multiple" size="5">
   {section name=ix loop=$categories}
    <option value="{$categories[ix].categId|escape}" {if $categories[ix].incat eq 'y'}selected="selected"{/if}>{if $categories[ix].categpath}{$categories[ix].categpath}{else}{$categories[ix].name}{/if}</option>
   {/section}
   </select><br />
  {if $mandatory_category >=0}
    <input type="hidden" name="cat_categorize" value="on" />
  {else}
   <label for="cat-check">{tr}categorize this object{/tr}:</label>
    <input type="checkbox" name="cat_categorize" id="cat-check" {if $cat_categorize eq 'y' or $categ_checked eq 'y'}checked="checked"{/if}/><br />
{if $prefs.feature_help eq 'y'}
  <div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}Uncheck the above checkbox to uncategorize this page/object{/tr}.</div>
  </div>
  <br />
{/if}
   {/if}
  {else}
    {tr}No categories defined{/tr} <br />
  {/if}
  {if $tiki_p_admin_categories eq 'y'}
    <a href="tiki-admin_categories.php" class="link">{tr}Admin categories{/tr}</a>
  {/if}
  </div>
  </td>
</tr>
{/if}

