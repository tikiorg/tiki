{if $prefs.feature_page_title ne 'n'}<h1>{$info.pageName}</h1>{/if}
<div class="wikitext">{$info.parsed}</div>
<div id="comment-container-{$info.page_id|escape}" data-target="tiki-ajax_services.php?controller=comment&amp;action=list&amp;type=wiki+page&amp;objectId={$info.pageName|escape:'url'}"></div>
{jq}
	var id = '#comment-container-{{$info.page_id}}';
	$(id).comment_load($(id).data('target'));
{/jq}
