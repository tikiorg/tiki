{if $feature_categories eq 'y' and (count($categories) gt 0 or $tiki_p_admin_categories eq 'y')}
<tr>
 <td class="formcolor">{tr}categorize{/tr}</td>
 <td class="formcolor">
  [<a class="link" href="javascript:show('categorizator');">{tr}show categories{/tr}</a>
  |<a class="link" href="javascript:hide('categorizator');">{tr}hide categories{/tr}</a>]
  <div id="categorizator" {if $cat_categorize eq 'n'}style="display:none;"{else}style="display:block;"{/if}>
  {if count($categories) gt 0}
   <select name="cat_categories[]" multiple="multiple" size="5">
   {section name=ix loop=$categories}
    <option value="{$categories[ix].categId}" {if $categories[ix].incat eq 'y'}selected="selected"{/if}>{$categories[ix].name}</option>
   {/section}
   </select>
   {tr}categorize this object{/tr}:
    <input type="checkbox" name="cat_categorize" {if $cat_categorize eq 'y'}checked="checked"{/if}/><br/>
  {else}
    No categories defined <br/>
  {/if}
  {if $tiki_p_admin_categories eq 'y'}
    <a href="tiki-admin_categories.php" class="link">{tr}Admin categories{/tr}</a>
  {/if}
  </div>
  </td>
</tr>
{/if}{* $feature_categories eq 'y' *}