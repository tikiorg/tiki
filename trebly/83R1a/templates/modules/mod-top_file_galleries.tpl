{* $Id: mod-top_file_galleries.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_file_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopFileGalleries nonums=$nonums}
	{section name=ix loop=$modTopFileGalleries}
		<li>
			<a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modTopFileGalleries[ix].id}">
				{$modTopFileGalleries[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
