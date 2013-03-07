{tikimodule error=$module_params.error title=$tpl_module_title name="Similar Content" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

{section name=ix loop=$similarContent}
<div class="{cycle} freetagitemlist" >
{$similarContent[ix].type} : <a href="{$similarContent[ix].href}">{$similarContent[ix].name|escape}</a>
</div>				
{/section}

{/tikimodule}

