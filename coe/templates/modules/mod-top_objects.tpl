{tikimodule error=$module_params.error title=$tpl_module_title name="top_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopObjects}
<li>
{$modTopObjects[ix]->object|escape} ({$modTopObjects[ix]->type})
</li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
