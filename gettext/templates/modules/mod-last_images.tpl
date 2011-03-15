{tikimodule error=$module_params.error title=$tpl_module_title name="last_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastImages nonums=$nonums}
	{section name=ix loop=$modLastImages}
		<li>
		{if $content eq "thumbnails"}
			<span class="module">
				<a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
					<img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{$modLastImages[ix].name|escape}" alt="{$modLastImages[ix].description|escape}" />
				</a>
			</span>
			{if strstr($smarty.server.PHP_SELF, 'tiki-editpage.php')}
			<span class="module">
				<a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}{literal}}{/literal}');">
					{tr}insert original{/tr}
				</a>
				::
				<a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1{literal}}{/literal}');">{tr}insert thumbnail{/tr}</a>
			</span>
			{/if}
		{else}
			<a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">{$modLastImages[ix].name|escape}</a>
		{/if}
		</li>
	{/section}
{/modules_list}
{/tikimodule}
