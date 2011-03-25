{if $prefs.feature_page_title ne 'n'}<h1>{$info.pageName}</h1>{/if}
<div class="wikitext">{$info.parsed}</div>
{if $comments_cant}
	{include file="comments.tpl"}
{/if}
