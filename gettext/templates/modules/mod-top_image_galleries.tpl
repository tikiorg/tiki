{tikimodule error=$module_params.error title="{tr}Top galleries{/tr}" name="top_image_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopGalleries nonums=$nonums}
	{section name=ix loop=$modTopGalleries}
		<li>
			<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modTopGalleries[ix].galleryId}">
				{$modTopGalleries[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
