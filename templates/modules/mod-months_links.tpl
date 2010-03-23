{if isset($months)}
{tikimodule error=$module_params.error title=$tpl_module_title name="months_links" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$months nonums=$nonums}
	{foreach key=month item=link from=$months}
		<li><a href="{$link}">{$month}</a></li>
	{/foreach}
{/modules_list}
{/tikimodule}
{/if}
