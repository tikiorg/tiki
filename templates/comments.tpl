{* $Id$ *}

<div>

{if $tiki_p_forum_read eq 'y'}

	{* This section (comment) is only displayed *}
	{* if a reply to it is being composed *}
	{* The $parent_com is only set in this case*}
	{* WARNING: when previewing a new reply to a forum post, $parent_com is also set *}

	{if $comments_cant gt 0}
		<form method="get" id="comment-form" class="comments">
			{section name=i loop=$comments_request_data}
				<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}">
			{/section}
			{*for the forum service button when js is not enabled*}
			<input type="hidden" name="controller" value="forum">
			<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}">
			<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}">
			<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}">
			<input type="hidden" name="comments_objectId" value="{$comments_objectId|escape}">
			<input type="hidden" name="comments_offset" value="0">
			<input type="hidden" name="comments_coms" value="{$topics_encoded|escape}">
			{if $smarty.request.topics_offset}<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}">{/if}
			{if $smarty.request.topics_find}<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}">{/if}
			{if $smarty.request.topics_sort_mode}<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}">{/if}
			{if $smarty.request.topics_threshold}<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}">{/if}
			{if $forumId}<input type="hidden" name="forumId" value="{$forumId|escape}">{/if}

			{if $tiki_p_admin_forum eq 'y'}
				<div class="panel panel-primary form-group">
					<div class="panel-heading">
						{tr}Moderator actions for selected topics{/tr}
					</div>
					<div class="panel-body form-inline">
						<span class="infos pull-right">
							{if $reported > 0}
								<a class="btn btn-default btn-sm tips" href="tiki-forums_reported.php?forumId={$forumId}" title=":{tr}Reported messages{/tr}">{tr}Reported{/tr} <span class="badge">{$reported}</span></a>
							{/if}
							{if $queued > 0}
								<a class="btn btn-default btn-sm tips" href="tiki-forum_queue.php?forumId={$forumId}" title=":{tr}Queued messages{/tr}">{tr}Queued{/tr} <span class="badge">{$queued}</span></a>
							{/if}
						</span>
						{if $topics|@count > 1}
							<button
								type="submit" formaction="{bootstrap_modal controller=forum action=merge_topic}" title=":{tr}Merge{/tr}"
								form="comment-form"
								class="btn btn-default btn-sm tips confirm-submit"
							>
								{icon name="merge"}
							</button>
						{/if}
						<button
							type="submit" formaction="{bootstrap_modal controller=forum action=delete_topic}" title=":{tr}Delete{/tr}"
							form="comment-form"
							class="btn btn-default btn-sm tips confirm-submit"
						>
							{icon name="remove"}
						</button>
					</div>
				</div>
			{/if}

			{if $prefs.forum_thread_user_settings eq 'y'}
				{if $comments_cant > 0 and $section eq 'blogs'}
					{* displaying just for blogs only because I'm not sure if this is useful for other sections *}
					{capture name=comments_cant_title}{if $comments_cant == 1}{tr _0=$comments_cant}%0 comment{/tr}{else}{tr _0=$comments_cant}%0 comments{/tr}{/if}{/capture}
					<h3>{$smarty.capture.comments_cant_title}</h3>
				{/if}
				{if $comments_cant > $prefs.forum_thread_user_settings_threshold}
					<div class="forum_actions">
						<div class="actions">
							<span class="action">
								<label for="comments-maxcomm">{tr}Messages:{/tr}</label>
								<select name="comments_per_page" id="comments-maxcomm">
									<option value="10" {if $comments_per_page eq 10}selected="selected"{/if}>10</option>
									<option value="20" {if $comments_per_page eq 20}selected="selected"{/if}>20</option>
									<option value="30" {if $comments_per_page eq 30}selected="selected"{/if}>30</option>
									<option value="999999" {if $comments_per_page eq 999999}selected="selected"{/if}>{tr}All{/tr}</option>
								</select>

								{if $forum_info.is_flat neq 'y'}
									<label for="comments-style">{tr}Style:{/tr}</label>
									<select name="thread_style" id="comments-style">
										<option value="commentStyle_plain" {if $thread_style eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
										<option value="commentStyle_threaded" {if $thread_style eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
										<option value="commentStyle_headers" {if $thread_style eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers only{/tr}</option>
									</select>
								{/if}

								<label for="comments-sort">{tr}Sort:{/tr}</label>
								<select name="thread_sort_mode" id="comments-sort">
									<option value="commentDate_desc" {if $thread_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
									<option value="commentDate_asc" {if $thread_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
									{if $forum_info.vote_threads eq 'y'}
										<option value="points_desc" {if $thread_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
									{/if}
									<option value="title_desc" {if $thread_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
									<option value="title_asc" {if $thread_sort_mode eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
								</select>

								{if $forum_info.vote_threads eq 'y'}
									<label for="comments-thresh">{tr}Threshold:{/tr}</label>
									<select name="comments_threshold" id="comments-thresh">
										<option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
										<option value="0.01" {if $comments_threshold eq '0.01'}selected="selected"{/if}>0</option>
										<option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
										<option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
										<option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
										<option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
									</select>
								{/if}

								<label for="comments-search">{tr}Search:{/tr}</label>
								<input type="text" size="7" name="comments_commentFind" id="comments-search" value="{$comments_commentFind|escape}">

								<input type="submit" class="btn btn-default btn-sm" name="comments_setOptions" value="{tr}Set{/tr}">
							</span>
						</div>
					</div>
				{/if}
			{/if}

			{section name=rep loop=$comments_coms}
				{include file='comment.tpl' comment=$comments_coms[rep]}
				{if $thread_style != 'commentStyle_plain'}<br>{/if}
			{/section}
		</form>

		<div class="thread_pagination">
			{if $comments_threshold ne 0}
				<div class="nb_replies">
					{$comments_below} {if $comments_below eq 1}{tr}Reply{/tr}{else}{tr}Replies{/tr}{/if} {tr}below your current threshold{/tr}
				</div>
			{/if}

			{if $comments_cant_pages gt 1}
				<div class="center-block">
					{if $comments_prev_offset >= 0 && ! $display eq ''}
						[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_prev_offset}{$thread_sort_mode_param}&amp;comments_per_page={$comments_per_page}&amp;thread_style={$thread_style}">{tr}Prev{/tr}</a>]&nbsp;
					{/if}

					{tr}Page:{/tr} {$comments_actual_page}/{$comments_cant_pages}

					{if $comments_next_offset >= 0 && $display eq ''}
						&nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_next_offset}{$thread_sort_mode_param}&amp;comments_per_page={$comments_per_page}&amp;thread_style={$thread_style}">{tr}Next{/tr}</a>]
					{/if}

					{if $prefs.direct_pagination eq 'y' && $display eq ''}
						<br>
						{section loop=$comments_cant_pages name=foo}
							{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_per_page}
							<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$selector_offset}{$thread_sort_mode_param}&amp;comments_per_page={$comments_per_page}&amp;thread_style={$thread_style}">
							{$smarty.section.foo.index_next}</a>&nbsp;
						{/section}
					{/if}
				</div>
			{/if}
		</div>
	{/if}
{/if} {* end read comment *}
{if $section eq 'forums'}<a id="comments"></a>{/if}
{if !empty($errors)}
	{remarksbox type="warning" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error name=error}
			{if !$smarty.foreach.error.first}<br>{/if}
			{$error|escape}
		{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="note"}
		{foreach from=$feedbacks item=feedback name=feedback}
			{$feedback|escape}
			{if !$smarty.foreach.feedback.first}<br>{/if}
		{/foreach}
	{/remarksbox}
{/if}

{* Post dialog *}
{if $tiki_p_forum_post eq 'y'}
	{if $thread_is_locked eq 'y'}
		{assign var='lock_text' value="{tr}This thread is locked{/tr}"}
		{remarksbox type="note" title="{tr}Note{/tr}" icon="lock"}{$lock_text}{/remarksbox}
	{else}
		<div id="form">
			{if $post_reply > 0 || $edit_reply > 0 || $comment_preview}
				{* posting, editing or previewing a reply: show form *}
				<div id='{$postclass}open' class="threadpost">
			{else}
				<input type="button" name="comments_postComment" value="{tr}New Reply{/tr}" onclick="flip('{$postclass}');">
				<div id='{$postclass}' class="threadpost">
			{/if}

			<div>
				<h3>
				{if $comments_threadId > 0}{tr}Editing reply{/tr}{elseif $comment_preview eq 'y'}{tr}Preview{/tr}{elseif $parent_com}{tr}Reply to the selected post{/tr}{else}{tr}Post new message{/tr}{/if}
				</h3>
			</div>

			{if $comment_preview eq 'y'}
				{include file='comment.tpl' comment=$comment_preview_data}
			{/if}

			<form class="form-horizontal" role="form" enctype="multipart/form-data" method="post" action="{$comments_complete_father}#comments" id='editpostform'>
				<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}">
				<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}">
				<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}">
				<input type="hidden" name="comments_offset" value="{$comments_offset|escape}">
				<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}">
				<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}">
				<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}">
				<input type="hidden" name="comments_objectId" value="{$comments_objectId|escape}">
				<input type="hidden" name="comments_title" value="{if isset($page) and $page}{$page|escape}{/if}">

				{* Traverse request variables that were set to this page adding them as hidden data *}
				{section name=i loop=$comments_request_data}
					<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}">
				{/section}

				{if !$user}
					<div class="form-group">
						<label class="col-sm-2 control-label" for="anonymous_name">{tr}Name{/tr}</label>
						<div class="col-sm-10">
							<input type="text" maxlength="50" size="30" id="anonymous_name" name="anonymous_name" value="{$comment_preview_data.name|escape}">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="anonymous_email">{tr}If you would like to be notified when someone replies to this topic<br>please tell us your e-mail address{/tr}</label>
						<div class="col-sm-10">
								<input type="text" size="30" id="anonymous_email" name="anonymous_email" value="{$comment_preview_data.email|escape}">
						</div>
					</div>
				{/if}

				{if $prefs.forum_reply_notitle neq 'y'}
					<div class="form-group">
						<label class="col-sm-2 control-label" for="comments-title">{tr}Title{/tr} <span class="text-danger">*</span> </label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="comments_title" id="comments-title" value="{$comment_title|escape}">
						</div>
					</div>
				{/if}

				<div class="form-group">
					<label class="col-sm-2 control-label" for="editpost2">{tr}Reply{/tr}</label>
					<div class="col-sm-10">
						{if $prefs.feature_wysiwyg eq 'y' and $prefs.wysiwyg_htmltowiki eq 'y' and $prefs.feature_forum_parse eq 'y'}
							{$forum_wysiwyg = 'y'}
						{else}
							{$forum_wysiwyg = 'n'}
						{/if}
						{textarea rows="10" codemirror='true' syntax='tiki' id="editpost2" class="form-control" name="comments_data" _wysiwyg=$forum_wysiwyg}{strip}
							{*If set to reply not empty, if you are editing a post, or previewing, put the contents in the text area.*}
							{if ($prefs.feature_forum_replyempty ne 'y') || $edit_reply > 0 || $comment_preview eq 'y' || !empty($errors)}
								{$comment_data}
							{/if}
						{/strip}{/textarea}
						{if $user and $prefs.feature_user_watches eq 'y'}
							<div id="watch_thread_on_reply">
								<input id="watch_thread" type="checkbox" name="watch" value="y"{if $user_watching_topic eq 'y' or $smarty.request.watch eq 'y'} checked="checked"{/if}> <label for="watch_thread">{tr}Send me an email when someone replies{/tr}</label>
							</div>
						{/if}
					</div>
				</div>

				{if ($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and ($tiki_p_admin_forum eq 'y' or $forum_info.moderator == $user)) or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y')}
					{assign var='can_attach_file' value='y'}
					<div class="form-group">
						<label class="col-sm-2 control-label" for="userfile1">
							{tr}Attach a file{/tr}
						</label>
						<div class="col-sm-10">
							<input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}"><input class="form-control" id="userfile1" name="userfile1" type="file">{tr}Maximum size:{/tr} {$forum_info.att_max_size|kbsize}
						</div>
					</div>
				{/if}

				{if $prefs.feature_contribution eq 'y'}
					{include file='contribution.tpl' in_comment="y"}
				{/if}

				{if $prefs.feature_antibot eq 'y'}
					{assign var='showmandatory' value='y'}
					{include file='antibot.tpl' td_style="formcolor"}
				{/if}

				<div class="form-group">
					{if isset($parent_coms) and $parent_coms}
						<label class="col-sm-2 control-label" for="comments_postComment">
							{tr}Reply to parent post{/tr}
						</label>
					{else}
						<label class="col-sm-2 control-label" for="comments_postComment">
							{tr}Post new reply{/tr}
						</label>
					{/if}
					<div class="col-sm-10">
						<input type="submit" class="btn btn-default btn-sm" id="comments_postComment" name="comments_postComment" value="{tr}Post{/tr}" onclick="needToConfirm=false;">
						{if !empty($user) && $prefs.feature_comments_post_as_anonymous eq 'y'}
							<input type="submit" class="btn btn-default btn-sm" name="comments_postComment_anonymous" value="{tr}Post as Anonymous{/tr}" onclick="needToConfirm=false;">
						{/if}
						<input type="submit" class="btn btn-default btn-sm" name="comments_previewComment" id="comments_previewComment" value="{tr}Preview{/tr}">
						{jq}$("#comments_previewComment").click(function () {
	if ($("#userfile1").val()) {
		alert("{tr}Please note that the preview does not keep the attached file which you will have to choose before posting.{/tr}");
	}
	needToConfirm=false;
});{/jq}
						<input type="submit" class="btn btn-default btn-sm" name="comments_cancelComment" value="{tr}Cancel{/tr}" onclick="hide('{$postclass}open'); return false">
					</div>
				</div>
			</form>

			{assign var=tips_title value="{tr}Posting replies{/tr}"}

			</div>
		</div>
	{/if}
{/if}
</div>
{if $prefs.forum_reply_forcetitle eq 'y'}
{jq}
$('#editpostform').submit( function() {
	if (!$('#comments-title').val()) {
		alert('{tr}Please enter a title{/tr}');
		return false;
	}
});
{/jq}
{/if}
{jq}
	var $forum = $("#editpostform");
	$forum.submit(function() {
		// prevent double submission
		if (!$forum.data("sub")) {
			$forum.tikiModal('Save in Progress...');
			$forum.data("sub", true);
			$forum.submit();
		}
	});
{/jq}
{* End of Post dialog *}

