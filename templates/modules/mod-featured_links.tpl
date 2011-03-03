{if $prefs.feature_featuredLinks eq 'y'}
	{tikimodule error=$module_params.error title=$tpl_module_title name="featured_links" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{modules_list list=$featuredLinks nonums=$nonums}
		{section name=ix loop=$featuredLinks}
			<li>
			{if $featuredLinks[ix].type eq 'f'}
				<a class="linkmodule" href="tiki-featured_link.php?type={$featuredLinks[ix].type}&amp;url={$featuredLinks[ix].url|escape:"url"}">{$featuredLinks[ix].title|escape}</a>
			{else}
				<a class="linkmodule" {if $featuredLinks[ix].type eq 'n'}target='_blank'{/if} href="{$featuredLinks[ix].url}">{$featuredLinks[ix].title|escape}</a>
			{/if}
			</li>
		{/section}
	{/modules_list}
	{/tikimodule}
{/if}
