{tikimodule error=$module_params.error title=$tpl_module_title name="random_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div id="random_image">
		{if !isset($module_params.showlink) or $module_params.showlink ne 'n'}
			<a class="linkmodule" href="tiki-browse_image.php?imageId={$img.imageId}">
		{/if}
		<img src="show_image.php?id={$img.imageId}{if empty($module_params.thumb) or $module_params.thumb eq 'y'}&amp;thumb=1{/if}" alt="{tr}Image{/tr}">
		{if isset($module_params.showname) or $module_params.showname eq 'y'}
			<div class="name">{$img.name|escape}</div>
		{/if}
		{if !isset($module_params.showlink) or $module_params.showlink ne 'n'}
			</a>
		{/if}
		{if isset($module_params.showdescription) and $module_params.showdescription eq 'y'}
			<div class="description help-block">{$img.description|escape}</div>
		{/if}
	</div>
{/tikimodule}
