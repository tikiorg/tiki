<!-- START of {$smarty.template} -->{section name=ix loop=$listpages}
	<div class="articletitle">
		<span class="newsitem">
			<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}">{$listpages[ix].title}</a>
		</span>
		<br />
	</div>
{/section}<!-- END of {$smarty.template} -->
