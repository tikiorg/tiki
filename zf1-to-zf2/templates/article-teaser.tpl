<div id="article-teaser-list">
	{foreach name="teaser" item=result from=$results}
		{block name="teaser"}
			<div class="article-teaser clearfix">
				<h4 class="article-title">{object_link id="`$result.object_id`" type="`$result.object_type`"} {if $result.published neq 'y'}<span class=text-muted>(Not Published)</span>{/if}</h4>
				<div class="article-info">By {$result.article_author|userlink}</div>
				<div class="article-snippet">
					{$result.art_content|escape}
				</div>
			</div>
		{/block}
		{if not $smarty.foreach.teaser.last}
			<hr/>
		{/if}
	{/foreach}
</div>