{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_file_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastFileGalleries nonums=$nonums}
	{section name=ix loop=$modLastFileGalleries}
		<li>
			<a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].id}">
				{$modLastFileGalleries[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
