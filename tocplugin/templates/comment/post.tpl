{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $threadId}
		<div class="alert alert-success">
			{if $prefs.feature_comments_moderation eq 'y'}
				<p>{tr}Your message has been queued for approval and will be posted after a moderator approves it.{/tr}</p>
			{else}
				<p>{tr}Your comment was posted.{/tr}</p>
			{/if}
			<p>{tr}Go back to:{/tr} {object_link type=$type objectId=$objectId}</p>
		</div>
	{else}
		<form method="post" action="{service controller=comment action=post}" role="form">
			<div class="panel panel-default">
				{if ! $user or $prefs.feature_comments_post_as_anonymous eq 'y'}
					<div class="panel-heading">
						{if $user}
							{remarksbox type=warning title="Anonymous posting"}
								{tr}You are currently registered on this site. This section is optional. By filling it, you will not link this post to your account and preserve your anonymity.{/tr}
							{/remarksbox}
						{/if}
						<div class="form-inline">
							<div class="form-group">
								<label class="clearfix" for="comment-anonymous_name">{tr}Name{/tr}</label>
								<input type="text" name="anonymous_name" id="comment-anonymous_name" value="{$anonymous_name|escape}"/>
							</div>
							<div class="form-group">
								<label class="clearfix" for="comment-anonymous_email">{tr}Email{/tr}</label>
								<input type="email" id="comment-anonymous_email" name="anonymous_email" value="{$anonymous_email|escape}"/>
							</div>
							<div class="form-group">
								<label class="clearfix" for="comment-anonymous_website">{tr}Website{/tr}</label>
								<input type="url" id="comment-anonymous_website" name="anonymous_website" value="{$anonymous_website|escape}"/>
							</div>
						</div>
					</div>
				{/if}
				<div class="panel-body">
					<input type="hidden" name="type" value="{$type|escape}"/>
					<input type="hidden" name="objectId" value="{$objectId|escape}"/>
					<input type="hidden" name="parentId" value="{$parentId|escape}"/>
					<input type="hidden" name="post" value="1"/>
					{if $prefs.comments_notitle neq 'y'}
						<div class="form-group">
							<label for="comment-title" class="clearfix comment-title">{tr}Title{/tr}</label>
							<input type="text" id="comment-title" name="title" value="{$title|escape}" class="form-control" placeholder="Comment title"/>
						</div>
					{/if}
					{capture name=rows}{if $type eq 'forum'}{$prefs.default_rows_textarea_forum}{else}{$prefs.default_rows_textarea_comment}{/if}{/capture}
					{textarea codemirror='true' syntax='tiki' name="data" comments="y" _wysiwyg="n" rows=$smarty.capture.rows class="form-control wikiedit" placeholder="{tr}Post new comment{/tr}..."}{$data|escape}{/textarea}
				</div>
				<div class="panel-footer">
					{if $prefs.feature_antibot eq 'y'}
						{assign var='showmandatory' value='y'}
						{include file='antibot.tpl'}
					{/if}
					<input type="hidden" name="return_url" value="{$return_url|escape}">
					<div class="form-group comment-post">
						<input type="submit" class="comment-post btn btn-primary btn-sm" value="{tr}Post{/tr}"/>
						<div class="btn btn-link">
							<a href="#" onclick="$(this).closest('.comment-container').reload(); $(this).closest('.ui-dialog').remove(); return false;">{tr}Cancel{/tr}</a>
						</div>
					</div>
				</div>
			</div>
		</form>
	{/if}
	{if $prefs.feature_syntax_highlighter eq 'y'}
		{jq}
			//Synchronize textarea and codemirror before comment is posted
			$(".comment-form>form").submit(function(event){
				var $textarea = $(event.target).find("textarea.wikiedit"); //retrieve the text area from the form that is submitted
				if (typeof syntaxHighlighter.sync === 'function') {
					syntaxHighlighter.sync($textarea);
				}
			});
		{/jq}
	{/if}
{/block}
