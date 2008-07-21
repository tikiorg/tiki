{if $user}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My prefered tags{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="freetags_prefered" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {foreach from=$most_popular_tags item=tag}
  <a class="freetag_{$tag.size}" href="tiki-browse_freetags.php?tag={$tag.tag|escape:'url'}">{$tag.tag}</a> 
  {/foreach}
  {/tikimodule}
{/if}
