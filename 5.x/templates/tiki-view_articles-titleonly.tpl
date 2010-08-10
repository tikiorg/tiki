{if !empty($container_class)}<div class="{$container_class}">{/if}
{section name=ix loop=$listpages}
	<div class="articletitle">
		<span class="newsitem">
			<a href="{$listpages[ix].articleId|sefurl:article}">{$listpages[ix].title|escape}</a>
		</span>
		<br />
	</div>
{/section}
{if !empty($container_class)}</div>{/if}
