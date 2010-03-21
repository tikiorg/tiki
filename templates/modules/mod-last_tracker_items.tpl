{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_tracker_items" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if !empty($module_error)}{tr}{$module_error}{/tr}{/if}
{modules_list list=$modLastItems nonums=$nonums}
	{section name=ix loop=$modLastItems}
		<li>
			<a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastItems[ix].itemId}">
				{$modLastItems[ix].subject|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
