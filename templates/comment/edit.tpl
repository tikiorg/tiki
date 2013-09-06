<form method="post" action="{service controller=comment action=edit threadId=$comment.threadId}">
	<fieldset>
		<input type="hidden" name="edit" value="1"/>
		{if $prefs.comments_notitle neq 'y'}
			<label class="clearfix comment-title">{tr}Title:{/tr} <input type="text" name="title" value="{$comment.title|escape}"/></label>
		{/if}
		{capture name=rows}{if $type eq 'forum'}{$prefs.default_rows_textarea_forum}{else}{$prefs.default_rows_textarea_comment}{/if}{/capture}
		{textarea codemirror='true' syntax='tiki' name=data comments="y" _wysiwyg="n" rows=$smarty.capture.rows}{$comment.data}{/textarea}
		<input type="submit" class="clearfix comment-edit" value="{tr}Save{/tr}"/>
	</fieldset>
</form>
