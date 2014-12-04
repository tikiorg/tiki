{extends 'layout_view.tpl'}

{block name="title"}
	<h3>{tr}Comments{/tr}
		<span class="lock">
			{if ! $parentId && $allow_lock}
				{self_link controller=comment action=lock modal=true type=$type objectId=$objectId _icon=lock _class="confirm-prompt btn btn-default btn-sm" _bootstrap="y" _confirm="{tr}Do you really want to lock comments?{/tr}"}{tr}Lock{/tr}{/self_link}
			{/if}
			{if ! $parentId && $allow_unlock}
				{self_link controller=comment action=unlock type=$type objectId=$objectId _icon=lock_break _class="confirm-prompt btn btn-default btn-sm" _confirm="{tr}Do you really want to unlock comments?{/tr}"}{tr}Unlock{/tr}{/self_link}
			{/if}
		</span>
	</h3>
{/block}

{block name="content"}
	{if $cant gt 0}
		{include file="comment/list_inner.tpl"}
	{else}
		{remarksbox type=info title="{tr}No comments{/tr}"}
			{tr}There are no comments at this time.{/tr}
		{/remarksbox}
	{/if}

	{if $allow_post}
		<div class="submit">
			<h3>
				<div class="button buttons comment-form {if $prefs.wiki_comments_form_displayed_default eq 'y'}autoshow{/if}">
					<a class="btn btn-primary custom-handling" href="{service controller=comment action=post type=$type objectId=$objectId}" data-target="#add-comment-zone-{$objectId|replace:' ':''}">{tr}Post new comment{/tr}</a>
				</div>
		</div>
		<div id="add-comment-zone-{$objectId|replace:' ':''}" class="comment-form">
		</div>
	{/if}

	{if $prefs.feature_inline_comments eq 'y'}
		<a id="note-editor-comment" class="alert alert-warning" href="#">{tr}Add Comment{/tr}</a>
	{/if}

	<script type="text/javascript">
		var ajax_url = '{$base_url}';
		var objectId = '{$objectId}';
	</script>
{/block}
