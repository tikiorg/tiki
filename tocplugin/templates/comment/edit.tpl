{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=comment action=edit threadId=$comment.threadId}">
		<div class="panel panel-default">
			<div class="panel-heading">
				{tr}Edit Comment{/tr}
			</div>
			<fieldset>
				<input type="hidden" name="edit" value="1"/>
				<div class="panel-body">
				{if $prefs.comments_notitle neq 'y'}
					<div class="form-group">
						<label for="comment-title" class="clearfix comment-title">{tr}Title{/tr}</label>
						<input type="text" id="comment-title" name="title" value="{$comment.title|escape}" class="form-control" placeholder="Comment title"/>
					</div>
				{/if}
				{capture name=rows}{if $type eq 'forum'}{$prefs.default_rows_textarea_forum}{else}{$prefs.default_rows_textarea_comment}{/if}{/capture}
				{textarea codemirror='true' syntax='tiki' name=data comments="y" _wysiwyg="n" rows=$smarty.capture.rows}{$comment.data}{/textarea}
				</div>
				<div class="panel-footer">
					<input type="submit" class="clearfix comment-editclass btn btn-primary btn-sm" value="{tr}Save{/tr}"/>
					<div class="btn btn-link">
						<a href="#" onclick="$(this).closest('.comment-container').reload(); $(this).closest('.ui-dialog').remove(); return false;">{tr}Cancel{/tr}</a>
					</div>
				</div>
			</fieldset>
		</div>
	</form>
{/block}
