{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopImages nonums=$nonums}
	{section name=ix loop=$modTopImages}
		<li>
			<a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
			{if $content eq "thumbnails"}
				<img alt="image" src="show_image.php?id={$modTopImages[ix].imageId}&amp;thumb=1" />
			{else}
				{$modTopImages[ix].name|escape}
			{/if}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
