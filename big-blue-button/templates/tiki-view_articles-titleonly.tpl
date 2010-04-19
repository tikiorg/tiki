{if !empty($container_class)}<div class="{$container_class}">{/if}
{section name=ix loop=$listpages}
	<div class="articletitle">
		<span class="newsitem">
			<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}">{$listpages[ix].title}</a>
		</span>
		<br />
	</div>
{/section}
{if !empty($container_class)}</div>{/if}
