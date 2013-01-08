{* $Id$ *}

{if $show_semantic_links_module}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Page Relations{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="semantic_links" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{foreach from=$msl_relations key=msl_label item=msl_list}
		<h3>{$msl_label|escape}</h3>
		<ul>
			{foreach from=$msl_list key=msl_item item=msl_url}
				<li><a href="{$msl_url|escape}">{$msl_item|escape}</a></li>
			{/foreach}
		</ul>
	{/foreach}
{/tikimodule}
{/if}
