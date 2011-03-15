{tikimodule error=$module_params.error title=$tpl_module_title name="freetags_most_popular" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if ($type ne 'cloud')}<ul class="freetag">{/if}
{foreach from=$most_popular_tags item=tag}
{if ($type ne 'cloud')}<li class="freetag">{/if}
	{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
  	<a class="freetag_{$tag.size}" title="{tr}List everything tagged{/tr} {$tag.tag|escape}" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag|escape}</a>
  &nbsp;
{if ($type ne 'cloud')}</li>{/if}
{/foreach}
{if ($type ne 'cloud')}</ul>{/if}
{/tikimodule}
