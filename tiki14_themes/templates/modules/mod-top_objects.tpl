{tikimodule error=$module_params.error title=$tpl_module_title name="top_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopObjects nonums=$nonums}
	{section name=ix loop=$modTopObjects}
		<li>
			{$modTopObjects[ix]->object|escape} ({$modTopObjects[ix]->type})
		</li>
	{/section}
{/modules_list}
{/tikimodule}
