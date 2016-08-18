{if isset($preferred_tags)}
	{tikimodule error=$module_params.error title=$tpl_module_title name="freetags_prefered" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{foreach from=$most_popular_tags item=tag}
			{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
			<a class="freetag_{$tag.size}" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag|escape}</a>
		{/foreach}
	{/tikimodule}
{/if}
