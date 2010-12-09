{* $Id$ *}

{if $forum_mode eq 'y'}
	<div>
{else}
	<div id="comments">
	{if $pagemd5}
		{assign var=cookie_key value="show_comzone$pagemd5"}
	{else}
		{assign var=cookie_key value="show_comzone"}
	{/if}

	<div {*do not missed up with the space*}
		{if $pagemd5}
			id="comzone{$pagemd5}"
		{else}
			id="comzone"
		{/if}
		{if $show_comzone eq 'y' or $comments_show eq 'y'} {* force it *}
			style="display: block;"
		{elseif (isset($smarty.session.tiki_cookie_jar.$cookie_key) and $smarty.session.tiki_cookie_jar.$cookie_key neq 'y')} {* cookie gets stored here with JS only *}
			style="display: none;"
		{elseif ((!isset($smarty.session.tiki_cookie_jar.$cookie_key) and $prefs.wiki_comments_displayed_default neq 'y'))}
			style="display: none;"
		{else}
			style="display: block;"
		{/if}
	>
{/if}

{if ($tiki_p_read_comments eq 'y' and $forum_mode ne 'y') or ($tiki_p_forum_read eq 'y' and $forum_mode eq 'y')}

	{* This section (comment) is only displayed * }
	{* if a reply to it is being composed * }
	{* The $parent_com is only set in this case *}
	{* WARNING: when previewing a new reply to a forum post, $parent_com is also set *}

	{if $comments_cant gt 0}
		<form method="get" action="{$comments_father}" class="comments">
			{section name=i loop=$comments_request_data}
				<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
			{/section}
			<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />    
			<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
			<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
			<input type="hidden" name="comments_objectId" value="{$comments_objectId|escape}" />
			<input type="hidden" name="comments_offset" value="0" />
			{if $smarty.request.topics_offset}<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />{/if}
			{if $smarty.request.topics_find}<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />{/if}
			{if $smarty.request.topics_sort_mode}<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />{/if}
			{if $smarty.request.topics_threshold}<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />{/if}
			{if $forumId}<input type="hidden" name="forumId" value="{$forumId|escape}" />{/if}

			{if $tiki_p_admin_forum eq 'y' and $forum_mode eq 'y'}
				<div class="forum_actions">
					<div class="headers">
						<span class="title">{tr}Moderator actions{/tr}</span>
						<span class="infos">
							{if $reported > 0}
								<a class="link" href="tiki-forums_reported.php?forumId={$forumId}">{tr}reported:{/tr}{$reported}</a> |
							{/if}
							<a class="link" href="tiki-forum_queue.php?forumId={$forumId}">{tr}queued:{/tr}{$queued}</a>
						</span>
					</div>
					<div class="actions">
						{if $topics|@count > 1}
							<span class="action">
								{tr}Move to topic:{/tr}
								<select name="moveto">
									{section name=ix loop=$topics}
										{if $topics[ix].threadId ne $comments_parentId}
											<option value="{$topics[ix].threadId|escape}">{$topics[ix].title|truncate:100|escape}</option>
										{/if}
									{/section}
								</select>
								<input type="submit" name="movesel" value="{tr}Move{/tr}" />
							</span>
						{/if}

						<span class="action">
							<input type="submit" name="delsel" value="{tr}Delete Selected{/tr}" />
						</span>
					</div>
				</div>
			{/if}

			{if $forum_mode neq 'y' or $prefs.forum_thread_user_settings eq 'y'}
				{if $comments_cant > 0 and $section eq 'blogs'}
					{* displaying just for blogs only because I'm not sure if this is useful for other sections *}
					{capture name=comments_cant_title}{if $comments_cant == 1}{tr}{$comments_cant} comment{/tr}{else}{tr}{$comments_cant} comments{/tr}{/if}{/capture}
					<h3>{$smarty.capture.comments_cant_title}</h3>
				{/if}
				<div class="forum_actions">
					{if $forum_mode neq 'y'}
						<div class="headers">
						{if $tiki_p_admin_comments eq 'y' or $tiki_p_lock_comments eq 'y'}
							{if ($tiki_p_admin_comments eq 'y' and ($prefs.feature_comments_moderation eq 'y' || $prefs.comments_archive eq 'y')) or $prefs.feature_comments_locking eq 'y'}
								<span class="title">{tr}Moderator actions{/tr}</span>
							{/if}
							<span class="infos">
							{if $tiki_p_admin_comments eq 'y'}
								{if $prefs.feature_comments_moderation eq 'y'}
									<a class="link" href="tiki-list_comments.php?types_section={$section}&amp;findfilter_approved=n{if isset($blogId)}&amp;blogId={$blogId}{/if}">{tr}queued:{/tr} {$queued}</a>
									&nbsp;&nbsp;
								{/if}
								{if $prefs.comments_archive eq 'y' && $count_archived_comments > 0}
									<span class="button" id="comments_showArchived">
										<a>
											{if $count_archived_comments == 1}
												{tr 0=$count_archived_comments}Show %0 archived comment{/tr}
											{else}
												{tr 0=$count_archived_comments}Show %0 archived comments{/tr}
											{/if}
										</a>
									</span>
									<span class="button" id="comments_hideArchived" style="display: none;">
										<a>
											{if $count_archived_comments == 1}
												{tr 0=$count_archived_comments}Hide %0 archived comment{/tr}
											{else}
												{tr 0=$count_archived_comments}Hide %0 archived comments{/tr}
											{/if}
										</a>
									</span>
								{/if}
							{/if}
							{if $prefs.feature_comments_locking eq 'y'}
								{if $thread_is_locked eq 'y'}
									{tr}Comments Locked{/tr}
									{self_link comments_lock='n' _icon='lock_break'}{tr}Unlock{/tr}{/self_link}
								{else}
									{self_link comments_lock='y' _icon='lock_add'}{tr}Lock{/tr}{/self_link}
								{/if}
							{/if}
							</span>
						{elseif $thread_is_locked eq 'y' and $prefs.feature_comments_locking eq 'y'}
							<span class="infos">{tr}Comments Locked{/tr}</span>
						{/if}
						</div>
					{/if}

					{if $comments_cant > $prefs.forum_thread_user_settings_threshold}
						<div class="actions">
							<span class="action">
								<label for="comments-maxcomm">{tr}Messages{/tr}:</label>
								<select name="comments_per_page" id="comments-maxcomm">
									<option value="10" {if $comments_per_page eq 10 }selected="selected"{/if}>10</option>
									<option value="20" {if $comments_per_page eq 20 }selected="selected"{/if}>20</option>
									<option value="30" {if $comments_per_page eq 30 }selected="selected"{/if}>30</option>
									<option value="999999" {if $comments_per_page eq 999999 }selected="selected"{/if}>{tr}All{/tr}</option>
								</select>
								
								{if $forum_mode neq 'y' or $forum_info.is_flat neq 'y' }
									<label for="comments-style">{tr}Style{/tr}:</label>
									<select name="thread_style" id="comments-style">
										<option value="commentStyle_plain" {if $thread_style eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
										<option value="commentStyle_threaded" {if $thread_style eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
										<option value="commentStyle_headers" {if $thread_style eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
									</select>
								{/if}

								<label for="comments-sort">{tr}Sort{/tr}:</label>
								<select name="thread_sort_mode" id="comments-sort">
									<option value="commentDate_desc" {if $thread_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
									<option value="commentDate_asc" {if $thread_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
									{if ($forum_mode eq 'y' and $forum_info.vote_threads eq 'y') or $forum_mode neq 'y'}	
										<option value="points_desc" {if $thread_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
									{/if}
									<option value="title_desc" {if $thread_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
									<option value="title_asc" {if $thread_sort_mode eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
								</select>

								{if ($forum_mode eq 'y' and $forum_info.vote_threads eq 'y') or $forum_mode neq 'y'}
									<label for="comments-thresh">{tr}Threshold{/tr}:</label>
									<select name="comments_threshold" id="comments-thresh">
										<option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
										<option value="0.01" {if $comments_threshold eq '0.01'}selected="selected"{/if}>0</option>
										<option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
										<option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
										<option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
										<option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
									</select>
								{/if}

								<label for="comments-search">{tr}Search{/tr}:</label>
								<input type="text" size="7" name="comments_commentFind" id="comments-search" value="{$comments_commentFind|escape}" />

								<input type="submit" name="comments_setOptions" value="{tr}Set{/tr}" />
							</span>
						</div>
					{/if}
				</div>
			{/if}

			{section name=rep loop=$comments_coms}
				{include file='comment.tpl' comment=$comments_coms[rep]}
				{if $thread_style != 'commentStyle_plain'}<br />{/if}
			{/section}
			{if $prefs.feature_wiki_paragraph_formatting eq 'y'}
				{jq}
					$('.postbody dt:contains("note")')
						.closest('.postbody')
						.addnotes( $('#top') );
				{/jq}
			{/if}
		</form>

		<div class="thread_pagination">
			{if $comments_threshold ne 0}
				<div class="nb_replies">
					{$comments_below} {if $comments_below eq 1}{tr}Reply{/tr}{else}{tr}Replies{/tr}{/if} {tr}below your current threshold{/tr}
				</div>
			{/if}

			{if $comments_cant_pages gt 1}
				<div class="mini">
					{if $comments_prev_offset >= 0 && ! $display eq ''}
						[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_prev_offset}{$thread_sort_mode_param}&amp;comments_per_page={$comments_per_page}&amp;thread_style={$thread_style}">{tr}Prev{/tr}</a>]&nbsp;
					{/if}

					{tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}

					{if $comments_next_offset >= 0 && $display eq ''}
						&nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_next_offset}{$thread_sort_mode_param}&amp;comments_per_page={$comments_per_page}&amp;thread_style={$thread_style}">{tr}Next{/tr}</a>]
					{/if}

					{if $prefs.direct_pagination eq 'y' && $display eq ''}
						<br />
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

{if !empty($errors)}
	{remarksbox type="warning" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error name=error}
			{if !$smarty.foreach.error.first}<br />{/if}
			{$error|escape}
		{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="note"}
		{foreach from=$feedbacks item=feedback name=feedback}
			{$feedback|escape}
			{if !$smarty.foreach.feedback.first}<br />{/if}
		{/foreach}
	{/remarksbox}
{/if}

{* Post dialog *}
{if ($tiki_p_forum_post eq 'y' and $forum_mode eq 'y') or ($tiki_p_post_comments eq 'y' and $forum_mode ne 'y')}
	{if ( $forum_mode eq 'y' or $prefs.feature_comments_locking eq 'y' ) and $thread_is_locked eq 'y'}
		{if $forum_mode eq 'y'}
			{assign var='lock_text' value="{tr}This thread is locked{/tr}"}
		{else}
			{assign var='lock_text' value="{tr}Comments are locked{/tr}"}
		{/if}
		{remarksbox type="note" title="{tr}Note{/tr}" icon="lock"}{$lock_text}{/remarksbox}
	{elseif $forum_mode eq 'y' and $forum_is_locked eq 'y'}
		{assign var='lock_text' value="{tr}This forum is locked{/tr}"}
		{remarksbox type="note" title="{tr}Note{/tr}" icon="lock"}{$lock_text}{/remarksbox}
	{else}
		<div id="form">
			{if $forum_mode eq 'y'}
				{if $post_reply > 0 || $edit_reply > 0 || $comment_preview}
					{* posting, editing or previewing a reply: show form *}
					<div id='{$postclass}open' class="threadpost">
				{else}
					<input type="button" name="comments_postComment" value="{tr}New Reply{/tr}" onclick="flip('{$postclass}');" />
					<div id='{$postclass}' class="threadpost">
				{/if}
			{/if}

			<div>
				<h3>
				{if $forum_mode eq 'y'}
					{if $comments_threadId > 0}{tr}Editing reply{/tr}{elseif $comment_preview eq 'y'}{tr}Preview{/tr}{elseif $parent_com}{tr}Reply to the selected post{/tr}{else}{tr}Post new message{/tr}{/if}
				{else}
					{if $comments_threadId > 0}{tr}Editing comment{/tr}{elseif $parent_com}{tr}Reply to the selected comment{/tr}{else}{tr}Post new comment{/tr}{/if}
				{/if}
				</h3>
			</div>

			{if $comment_preview eq 'y'}
				{include file='comment.tpl' comment=$comment_preview_data}
			{/if}

			<form enctype="multipart/form-data" method="post" action="{$comments_complete_father}#comments" id='editpostform'>
				<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
				<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
				<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
				<input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
				<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
				<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
				<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
				<input type="hidden" name="comments_objectId" value="{$comments_objectId|escape}" />
				<input type="hidden" name="comments_title" value="{if $page}{$page|escape}{/if}" />

				{* Traverse request variables that were set to this page adding them as hidden data *}
				{section name=i loop=$comments_request_data}
					<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
				{/section}

				<table class="formcolor">
					{if !$user}
						<tr>
							<td><label for="anonymous_name">{tr}Name{/tr}</span></label></td>
							<td><input type="text" maxlength="50" size="30" id="anonymous_name" name="anonymous_name"  value="{$comment_preview_data.name|escape}"/></td>
						</tr>
						{if $forum_mode eq 'y' or $prefs.comments_field_email eq 'y'}
							<tr>
								<td>
									<label for="anonymous_email">
										{if $forum_mode eq 'y'}
											{tr}If you would like to be notified when someone replies to this topic<br />please tell us your e-mail address{/tr}
										{else}
											{tr}Email{/tr}	
										{/if}
									</label>
								</td>
								<td>
									<input type="text" size="30" id="anonymous_email" name="anonymous_email" value="{$comment_preview_data.email|escape}"/>
									{if $forum_mode neq 'y'}
										<span>{tr}(your email address will not be published){/tr}</span>
									{/if}
								</td>
							</tr>
						{/if}

						{if $forum_mode neq 'y' and $prefs.comments_field_website eq 'y'}
							<tr>
								<td>
									<label for="anonymous_website">{tr}Website{/tr}</label>
								</td>
								<td><input type="text" size="30" id="anonymous_website" name="anonymous_website"  value="{if !empty($comment_preview_data.website)}{$comment_preview_data.website|escape}{else}http://{/if}" /></td>
							</tr>
						{/if}
					{/if}

					{if ( $forum_mode != 'y' and $prefs.comments_notitle neq 'y' ) or $prefs.forum_reply_notitle neq 'y' && $forum_mode == 'y'}
						<tr>
							<td>
								<label for="comments-title">{tr}Title{/tr} <span class="attention">*</span> </label>
							</td>
							<td>
								<input type="text" name="comments_title" id="comments-title" value="{$comment_title|escape}" /> 
							</td>
						</tr>
					{/if}

					{if $comment_can_rate_article eq 'y'}
						<tr>
							<td><label for="comments-rating">{tr}Rating{/tr} </label></td>
							<td>
								<select name="comment_rating" id="comments-rating">
									<option value="" {if $comment_rating eq ''}selected="selected"{/if}>No</option>
									<option value="0" {if $comment_rating eq 0}selected="selected"{/if}>0</option>
									<option value="1" {if $comment_rating eq 1}selected="selected"{/if}>1</option>
									<option value="2" {if $comment_rating eq 2}selected="selected"{/if}>2</option>
									<option value="3" {if $comment_rating eq 3}selected="selected"{/if}>3</option>
									<option value="4" {if $comment_rating eq 4}selected="selected"{/if}>4</option>
									<option value="5" {if $comment_rating eq 5}selected="selected"{/if}>5</option>
									<option value="6" {if $comment_rating eq 6}selected="selected"{/if}>6</option>
									<option value="7" {if $comment_rating eq 7}selected="selected"{/if}>7</option>
									<option value="8" {if $comment_rating eq 8}selected="selected"{/if}>8</option>
									<option value="9" {if $comment_rating eq 9}selected="selected"{/if}>9</option>
									<option value="10" {if $comment_rating eq 10}selected="selected"{/if}>10</option>
								</select>{tr}Rate this Article (10=best, 0=worse){/tr}
							</td>
						</tr>
					{/if}

					<tr>
						<td>
							<label for="editpost2">{if $forum_mode eq 'y'}{tr}Reply{/tr}{else}{tr}Comment{/tr} <span class="attention">*</span>{/if}</label>
						</td>
						<td>
							{textarea id="editpost2" name="comments_data" rows=$rows cols=$cols}{if ($forum_mode eq 'y' && $prefs.feature_forum_replyempty ne 'y') || $edit_reply > 0 || ($forum_mode neq 'y' && $post_reply > 0) || $comment_preview eq 'y' || !empty($errors)}{$comment_data}{/if}{/textarea} 
							<input type="hidden" name="rows" value="{$rows}" />
							<input type="hidden" name="cols" value="{$cols}" />
							{if $prefs.feature_wiki_paragraph_formatting eq 'y'}
								<a id="note-editor-comment" href="" style="display:none;">{tr}Add Comment{/tr}</a>
								{jq}
									$('#top').noteeditor('#editpostform textarea', '#note-editor-comment');
								{/jq}
							{/if}

							{if $forum_mode eq 'y' and $user and $prefs.feature_user_watches eq 'y'}
								<div id="watch_thread_on_reply">
									<input id="watch_thread" type="checkbox" name="watch" value="y"{if $user_watching_topic eq 'y' or $smarty.request.watch eq 'y'} checked="checked"{/if} /> <label for="watch_thread">{tr}Send me an e-mail when someone replies{/tr}</label>
								</div>
							{/if}
						</td>
					</tr>

					{if $forum_mode == "y" and (($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and ($tiki_p_admin_forum eq 'y'  or $forum_info.moderator == $user)) or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y'))}
						{assign var='can_attach_file' value='y'}
						<tr>
							<td>{tr}Attach file{/tr}</td>
							<td>
								<input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}" /><input id="userfile1" name="userfile1" type="file" />{tr}Maximum size:{/tr} {$forum_info.att_max_size|kbsize}
							</td>
						</tr>
					{/if}

					{if $prefs.feature_contribution eq 'y'}
						{include file='contribution.tpl' in_comment="y"}
					{/if}

					{if $prefs.feature_antibot eq 'y'}
						{assign var='showmandatory' value='y'}
						{include file='antibot.tpl' td_style="formcolor"}
					{/if}

					<tr>
						<td>
							{if $parent_coms}
								{tr}Reply to parent post{/tr}
							{else}
								{if $forum_mode eq 'y'}{tr}Post new reply{/tr}{/if}
							{/if}
						</td>

						<td>
							<input type="submit" id="comments_postComment" name="comments_postComment" value="{tr}Post{/tr}" onclick="needToConfirm=false;" />
							{if !empty($user) && $prefs.feature_comments_post_as_anonymous eq 'y'}
								<input type="submit" name="comments_postComment_anonymous" value="{tr}Post as Anonymous{/tr}" onclick="needToConfirm=false;" />
							{/if}
							<input type="submit" name="comments_previewComment" id="comments_previewComment" value="{tr}Preview{/tr}"
							{if ( isset($can_attach_file) && $can_attach_file eq 'y' ) or empty($user)}{strip}
								{assign var='file_preview_warning' value="{tr}Please note that the preview does not keep the attached file which you will have to choose before posting.{/tr}"}
								onclick="
								{if isset($can_attach_file) && $can_attach_file eq 'y'}
									if ($('#userfile1').val()) alert('{$file_preview_warning|escape:"javascript"}');
								{/if}
								"
							{/strip}{else} onclick="needToConfirm=false;"{/if} />
							{if $forum_mode eq 'y'}
								<input type="submit" name="comments_cancelComment" value="{tr}Cancel{/tr}" onclick="hide('{$postclass}'); return false" />
							{elseif $prefs.feature_comments_moderation eq 'y' and $tiki_p_admin_comments neq 'y'}
								{remarksbox type="note" title="{tr}Note{/tr}"}
									{tr}Your comment will have to be approved by the moderator before it is displayed.{/tr}
								{/remarksbox}	
							{/if}
						</td>
					</tr>
				</table>
			</form>

			<br />
			{if $forum_mode eq 'y'}
				{assign var=tips_title value="{tr}Posting replies{/tr}"}
			{else}
				{assign var=tips_title value="{tr}Posting comments{/tr}"}
			{/if}

			{if $forum_mode eq 'y'}
				</div>
			{/if}
		</div>
	{/if}
{/if}
</div>
{* End of Post dialog *}

{if $forum_mode neq 'y'}</div><!-- comzone end -->{/if}
