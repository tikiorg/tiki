{if $threadId}
	<p>{tr}Your comment was posted.{/tr}</p>
	<p>{object_link type=$type objectId=$objectId}</p>
{else}
	<form method="post" action="{service controller=comment action=post}">
		{if ! $user or $prefs.feature_comments_post_as_anonymous eq 'y'}
			<fieldset>
				{if $user}
					{remarksbox type=warning title="Anonymous posting"}
						{tr}You are currently registered on this site. This section is optional. By filling it, you will not link this post to your account and preserve your anonymity.{/tr}
					{/remarksbox}
				{/if}
				<label class="clearfix">{tr}Name:{/tr} <input type="text" name="anonymous_name" value="{$anonymous_name|escape}"/></label>
				<label class="clearfix">{tr}Email:{/tr} <input type="email" name="anonymous_email" value="{$anonymous_email|escape}"/></label>
				<label class="clearfix">{tr}Website:{/tr} <input type="url" name="anonymous_website" value="{$anonymous_website|escape}"/></label>
			</fieldset>
		{/if}
		<fieldset>
			<input type="hidden" name="type" value="{$type|escape}"/>
			<input type="hidden" name="objectId" value="{$objectId|escape}"/>
			<input type="hidden" name="parentId" value="{$parentId|escape}"/>
			<input type="hidden" name="post" value="1"/>
			{if $prefs.comments_notitle neq 'y'}
				<label class="clearfix comment-title">{tr}Title:{/tr} <input type="text" name="title" value="{$title|escape}"/></label>
			{/if}
			{capture name=rows}{if $type eq 'forum'}{$prefs.default_rows_textarea_forum}{else}{$prefs.default_rows_textarea_comment}{/if}{/capture}
			{textarea codemirror='true' syntax='tiki' name=data comments="y" _wysiwyg="n" rows=$smarty.capture.rows}{$data|escape}{/textarea}
			{if $prefs.feature_antibot eq 'y'}
				{assign var='showmandatory' value='y'}
				{include file='antibot.tpl'}
			{/if}
			<input type="submit" class="clearfix comment-post" value="{tr}Post{/tr}"/>
		</fieldset>
	</form>
{/if}