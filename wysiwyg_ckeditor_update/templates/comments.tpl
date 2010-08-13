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
{*Debug:<br />
comments_show: {$comments_show}<br />
show_comzone: {$show_comzone}<br />
prefs.wiki_comments_displayed_default: {$prefs.wiki_comments_displayed_default}<br />
prefs.show_comzone: {$prefs.show_comzone}<br />
cookie_key: {$cookie_key}<br />
smarty.session.tiki_cookie_jar.{$cookie_key}: {$smarty.session.tiki_cookie_jar.$cookie_key}<br />*}
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

{if !empty($errors)}
	{remarksbox type="warning" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error name=error}
			{if !$smarty.foreach.error.first}<br />{/if}
			{$error|escape}
		{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="feedback"}
		{foreach from=$feedbacks item=feedback name=feedback}
			{$feedback|escape}
			{if !$smarty.foreach.feedback.first}<br />{/if}
		{/foreach}
	{/remarksbox}
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
		{capture name=comments_cant_title}{if $comments_cant == 1}{tr}{$comments_cant} comment so far{/tr}{else}{tr}{$comments_cant} comments so far{/tr}{/if}{/capture}
		<h2>{$smarty.capture.comments_cant_title}</h2>
	{/if}
	<div class="forum_actions">
		{if $forum_mode neq 'y'}
			<div class="headers">
			{if $tiki_p_admin_comments eq 'y' or $tiki_p_lock_comments eq 'y'}
				{if ($tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y') or $prefs.feature_comments_locking eq 'y'}
					<span class="title">{tr}Moderator actions{/tr}</span>
				{/if}
				<span class="infos">
				{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
					<a class="link" href="tiki-list_comments.php?types_section={$section}&amp;findfilter_approved=n{if isset($blogId)}&amp;blogId={$blogId}{/if}">{tr}queued:{/tr} {$queued}</a>
					&nbsp;&nbsp;
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
	{jq}
		(function($) {
			$.fn.addnotes = function( container ) {
				return this.each(function(){
					var comment = this;
					var text = $('dt:contains("note")', comment).next('dd').text();
					var author = $('.author_info', comment).clone();
					var body = $('.postbody-content', comment).clone();
					body.find('dt:contains("note")').closest('dl').remove();

					if( text.length > 0 ) {
						var parents = container.find(':contains("' + text + '")').parent();
						var node = container.find(':contains("' + text + '")').not(parents)
							.addClass('highlight')
							.each( function() {
								var child = $('dl.note-list',this);
								if( ! child.length ) {
									child = $('<dl class="note-list"/>')
										.appendTo(this)
										.hide();

									$(this).click( function() {
										child.toggle();
									} );
								}

								child.append( $('<dt/>')
									.append(author) )
									.append( $('<dd/>').append(body) );
							} );
					}
				});
			};
		})($jq);

		$('.postbody dt:contains("note")')
			.closest('.postbody')
			.addnotes( $('#top') );
	{/jq}
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
		<h2 style="text-align: left">
		{if $forum_mode eq 'y'}
			{if $comments_threadId > 0}{tr}Editing reply{/tr}{elseif $comment_preview eq 'y'}{tr}Preview{/tr}{elseif $parent_com}{tr}Reply to the selected post{/tr}{else}{tr}Post new message{/tr}{/if}
		{else}
			{if $comments_threadId > 0}{tr}Editing comment{/tr}{elseif $parent_com}{tr}Reply to the selected comment{/tr}{else}{tr}Post new comment{/tr}{/if}
		{/if}
		</h2>
	</div>

	{if $comment_preview eq 'y'}
	<div class="clearfix post_preview" id="preview_comment">
		{jq}
			$(window).attr('location','#preview_comment');
		{/jq}
		{if $forum_mode neq 'y'}<b>{tr}Preview{/tr}</b>{/if}
		<div class="post"><div class="inner"><span class="corners-top"><span></span></span><div class="postbody">
			<div class="postbody-title"><div class="title">{$comments_preview_title|escape}</div></div>
			<div class="content">
				<div class="clearfix author">
					<span class="author_post_info">
						{tr}Published by{/tr} <span class="author_post_info_by">{if $user}{$user|userlink}{else}{$comments_preview_anonymous_name}{/if}</span>
						{if $comment_preview_date > 0}
							{tr}on{/tr} <span class="author_post_info_on">{$comment_preview_date|tiki_short_datetime}</span>
						{/if}
					</span>
				</div>
				{$comments_preview_data}
	  		</div>
		</div><span class="corners-bottom"><span></span></span></div></div>
	</div>
{*	<br class="clear" />*}
	{/if}

	<form enctype="multipart/form-data" method="post" action="{$comments_father}#comments" id='editpostform'>
	<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
	<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
	<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
	<input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
	<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
	<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
	<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
	<input type="hidden" name="comments_objectId" value="{$comments_objectId|escape}" />
	<input type="hidden" name="comments_title" value="{if $page}{$page|escape}{else}{tr}Untitled{/tr}{/if}" />

	{* Traverse request variables that were set to this page adding them as hidden data *}
	{section name=i loop=$comments_request_data}
	<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
	{/section}

	<table class="normal">
		{if !$user}
			<tr>
				<td class="formcolor"><label for="anonymous_name">{tr}Name{/tr}</span></label></td>
				<td class="formcolor"><input type="text" maxlength="50" id="anonymous_name" name="anonymous_name" /></td>
			</tr>
			{if $forum_mode eq 'y'}
				<tr>
					<td class="formcolor"><label for="anonymous_email">{tr}If you would like to be notified when someone replies to this topic<br />please tell us your e-mail address{/tr}</label></td>
					<td class="formcolor"><input type="text" size="30" id="anonymous_email" name="anonymous_email" /></td>
				</tr>
			{/if}
		{/if}

		{if ( $forum_mode != 'y' and $prefs.wiki_comments_notitle neq 'y' ) or $prefs.forum_reply_notitle neq 'y' && $forum_mode == 'y'}
			<tr>
				<td class="formcolor">
					<label for="comments-title">{tr}Title{/tr} <span class="attention">*</span> </label>
				</td>
				<td class="formcolor">
				{* 
				   Alain Désilets: This used to have a size="50" attribute, but I deleted it
				   because in the Collaborative_Multilingual_Terminology, we may need to view 
				   two different languages of the same page side by side. And the text length of
				   50 was causing the language displayed on the right side to be squished into a 
				   very narrow column, if comments were opened on the left side language
				   but not on the right side language.
				   
				   Unfortunately, without a size specification, the comments box looks 
				   a bit weird when we only view one language at a time.
				   
				   But I don't know how else to deal with this issue.
				 *}
					<input type="text" name="comments_title" id="comments-title" value="{$comment_title|escape}" /> 

				</td>
			</tr>
		{/if}

		{* Start: Xenfasa adding and testing article ratings in comments here. Not fully functional yet *}
		{if $comment_can_rate_article eq 'y'}
		<tr>
			<td class="formcolor"><label for="comments-rating">{tr}Rating{/tr} </label></td>
			<td class="formcolor">
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
				</select> Rate this Article (10=best, 0=worse)
			</td>
		</tr>
		{/if}
		{* End: Xenfasa adding and testing article ratings in comments here *}

		{if $prefs.section_comments_parse eq 'y' && $forum_mode neq 'y' || $prefs.feature_forum_parse eq 'y' && $forum_mode eq 'y'}
	        {assign var=toolbars_html value=true}{* can't find where this gets set in ui-revamp project *}
	        <tr>
	    		<td class="formcolor"></td>
	            <td class="formcolor">
	            	{toolbars area_id='editpost2' comments='y'}
	            </td>
	        </tr>
		{/if}
		<tr>
			<td class="formcolor">
				<label for="editpost2">{if $forum_mode eq 'y'}{tr}Reply{/tr}{else}{tr}Comment{/tr} <span class="attention">*</span>{/if}</label>
			</td>
			<td class="formcolor">
				<textarea id="editpost2" name="comments_data" rows="{$rows}" cols="{$cols}">{if $prefs.feature_forum_replyempty ne 'y' || $edit_reply > 0 || $comment_preview eq 'y'}{$comment_data|escape}{/if}</textarea> 
				<input type="hidden" name="rows" value="{$rows}" />
				<input type="hidden" name="cols" value="{$cols}" />
				{jq}
					var annote = $('<a href="">{tr}Comment{/tr}</a>')
						.css('background','white')
						.click( function( e ) {
							e.preventDefault();
							var annotation = $(this).attr('annotation');
							$(this).hide();

							$('#editpostform').parents().show();
							$('#editpostform textarea').val(';note:' + annotation + "\n\n").focus().scroll();
						} )
						.appendTo(document.body);

					$('#top').mouseup( function( e ) {
						var range;
						if( window.getSelection && window.getSelection().rangeCount ) {
							range = window.getSelection().getRangeAt(0);
						} else if( window.selection ) {
							range = window.selection.getRangeAt(0);
						}

						if( range ) {
							var string = $.trim( range.toString() );

							if( string.length && -1 === string.indexOf( "\n" ) ) {
								annote.attr('annotation', string);
								annote.show().position( {
									of: e,
									at: 'bottom left',
									my: 'top left',
									offset: '10 10'
								} );
							} else {
								annote.hide();
							}
						}
					} );
				{/jq}

				{if $forum_mode eq 'y' and $user and $prefs.feature_user_watches eq 'y'}
					<div id="watch_thread_on_reply">
						<input id="watch_thread" type="checkbox" name="watch" value="y"{if $user_watching_topic eq 'y' or $smarty.request.watch eq 'y'} checked="checked"{/if}> <label for="watch_thread">{tr}Send me an e-mail when someone replies{/tr}</label>
					</div>
				{/if}
			</td>
		</tr>


		{if $forum_mode == "y" and (($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and ($tiki_p_admin_forum eq 'y'  or $forum_info.moderator == $user)) or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y'))}
		{assign var='can_attach_file' value='y'}
		<tr>
			<td class="formcolor">{tr}Attach file{/tr}</td>
			<td class="formcolor">
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
			<td class="formcolor">
			{if $parent_coms}
				{tr}Reply to parent post{/tr}
			{else}
				{if $forum_mode eq 'y'}{tr}Post new reply{/tr}{/if}
			{/if}
			</td>

			<td class="formcolor">
				<input type="submit" name="comments_postComment" value="{tr}Post{/tr}" {if empty($user)}onclick="setCookie('anonymous_name',$('#anonymous_name').val());"{/if} />
				{if !empty($user) && $prefs.feature_comments_post_as_anonymous eq 'y'}
				<input type="submit" name="comments_postComment_anonymous" value="{tr}Post as Anonymous{/tr}" />
				{/if}
				<input type="submit" name="comments_previewComment" value="{tr}Preview{/tr}"
				{if ( isset($can_attach_file) && $can_attach_file eq 'y' ) or empty($user)}{strip}
					{assign var='file_preview_warning' value="{tr}Please note that the preview does not keep the attached file which you will have to choose before posting.{/tr}"}
					onclick="
					{if empty($user)}
						setCookie('anonymous_name',$('#anonymous_name').val());
					{/if}
					{if isset($can_attach_file) && $can_attach_file eq 'y'}
						if ($('#userfile1').val()) alert('{$file_preview_warning|escape:"javascript"}');
					{/if}
					"
				{/strip}{/if} />
				{if $forum_mode eq 'y'}
				<input type="button" name="comments_cancelComment" value="{tr}Cancel{/tr}" onclick="hide('{$postclass}');" />
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
{*</div>  now this tag causes problems instead of fixing (was added earlier to prevent side columns in *litecss themes from not appearing *}
{if empty($user) and $prefs.javascript_enabled eq "y"}
	{jq}
		var js_anonymous_name = getCookie('anonymous_name');
		if (js_anonymous_name) $('#anonymous_name').val( js_anonymous_name );
	{/jq}
{/if}
