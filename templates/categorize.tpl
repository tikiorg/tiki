{if $feature_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
<tr class="formcolor">
 <td>{tr}Categorize{/tr}</td>
 <td{if $cols} colspan="{$cols}"{/if}>
{if $mandatory_category >= 0}
  <div id="categorizator">
{else}
  [ <a class="link" href="javascript:show('categorizator');">{tr}show categories{/tr}</a>
  | <a class="link" href="javascript:hide('categorizator');">{tr}hide categories{/tr}</a> ]
  <div id="categorizator" {if $cat_categorize eq 'n' and $categ_checked ne 'y'}style="display:none;"{else}style="display:block;"{/if}>
{/if}
{if $feature_help eq 'y'}
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
{if $feature_help eq 'y'}
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
{/if}{* $feature_categories eq 'y' *}
