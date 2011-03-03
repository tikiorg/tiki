{tikimodule error=$module_params.error title=$tpl_module_title name="last_image_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastGalleries nonums=$nonums}
	{section name=ix loop=$modLastGalleries}
		<li>
			<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">
				{$modLastGalleries[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
