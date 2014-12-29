{if $mustread_module.object}
	{tikimodule error=$module_params.error title=$tpl_module_title name="mustread" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{service_inline controller=mustread action=object type=$mustread_module.object.type object=$mustread_module.object.object field=$mustread_module.field}
	{/tikimodule}
{/if}
