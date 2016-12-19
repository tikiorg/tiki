{tikimodule error=$module_params.error title=$tpl_module_title name="youtube" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<div style="text-align: center">
{foreach from=$data.xhtml item=video}
	<div>{$video}</div>
{/foreach}
{if isset($data.urls.user_home)}
	<div><a href="{$data.urls.user_home}" title="{tr}More Videos{/tr}" class="linkmodule">{tr}More Videos{/tr}</a></div>
{/if}
</div>
{/tikimodule}
