{* $Id$ *}
<div class="clearfix postbody-title">
	<div class="title">
		{if $post_list eq 'y'}
			<h2><a class="link" href="{$post_info.postId|sefurl:blogpost}">{$post_info.title|escape}</a></h2>
		{else}
			<h2>{$post_info.title|escape}</h2>
		{/if}
	</div>
	{include file='freetag_list.tpl' freetags=$post_info.freetags}
</div>
