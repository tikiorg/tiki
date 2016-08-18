{* $Id$ *}

{if $user}
	{tikimodule error=$module_params.error title=$tpl_module_title name="user_image_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{modules_list list=$modUserG nonums=$nonums}
			{section name=ix loop=$modUserG}
				<li>
					<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">
						{$modUserG[ix].name|escape}
					</a>
				</li>
			{/section}
		{/modules_list}
	{/tikimodule}
{/if}
