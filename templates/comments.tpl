{* $Id$ *}

{if $forum_mode eq 'y'}
<div>
{else}
<span id="comments" />
<div id="comzone"
{if (isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y') or (!isset($smarty.session.tiki_cookie_jar.show_comzone) and $prefs.wiki_comments_displayed_default eq 'y') or (isset($prefs.show_comzone) and $prefs.show_comzone eq 'y') or $show_comzone eq 'y' or $show_comments or $edit_reply eq '1'}
	style="display:block;"
{else}
	style="display:none;"
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
	<input type="hidden" name="comments_offset" value="0" />
	{if $smarty.request.topics_offset}<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />{/if}
	{if $smarty.request.topics_find}<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />{/if}
	{if $smarty.request.topics_sort_mode}<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />{/if}
	{if $smarty.request.topics_threshold}<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />{/if}

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
			<span class="action">
				{tr}Move to topic:{/tr}
				<select name="moveto">
				{section name=ix loop=$topics}
					{if $topics[ix].threadId ne $comments_parentId}
					<option value="{$topics[ix].threadId|escape}">{$topics[ix].title}</option>
					{/if}
				{/section}
				</select>
				<input type="submit" name="movesel" value="{tr}Move{/tr}" />
			</span>

			<span class="action">
				<input type="submit" name="delsel" value="{tr}Delete Selected{/tr}" />
			</span>
		</div>
	</div>
	{/if}

	{if $forum_mode neq 'y' or $prefs.forum_thread_user_settings eq 'y'}
	<div class="forum_actions">
		<div class="headers">
		</div>
		<div class="actions">
			<span class="action">

				{if $comments_cant > 10}
				<label for="comments-maxcomm">{tr}Messages{/tr}:</label>
				<select name="comments_per_page" id="comments-maxcomm">
					<option value="10" {if $comments_per_page eq 10 }selected="selected"{/if}>10</option>
					<option value="20" {if $comments_per_page eq 20 }selected="selected"{/if}>20</option>
					<option value="30" {if $comments_per_page eq 30 }selected="selected"{/if}>30</option>
					<option value="999999" {if $comments_per_page eq 999999 }selected="selected"{/if}>{tr}All{/tr}</option>
				</select>
				{/if}

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
	</div>
	{/if}

{*** Seems buggy (at least when called for a wiki page)
{if $forum_mode ne 'y'}
    <td class="heading" style="text-align: center; vertical-align: middle">
		<a class="link" href="{$comments_complete_father}comzone=hide">{tr}Hide all{/tr}</a>
    </td>
{/if}
***}


	{section name=rep loop=$comments_coms}
		{include file="comment.tpl" comment=$comments_coms[rep]}
		{if $thread_style != 'commentStyle_plain'}<br />{/if}
	{/section}
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
<span id="form" />
	{if $forum_mode eq 'y'}
		{if $post_reply > 0 || $edit_reply > 0 || $comment_preview}
			{* posting, editing or previewing a reply: show form *}
<div id='{$postclass}open' class="threadpost">
		{else}
<input type="button" name="comments_postComment" value="{tr}New Reply{/tr}" onclick="flip('{$postclass}');"/>
<div id='{$postclass}' class="threadpost">
		{/if}
	{/if}

	<div>
		<h2 style="text-align: left">
		{if $forum_mode eq 'y'}
			{if $comments_threadId > 0}{tr}Editing reply{/tr}{elseif $comment_preview eq 'y'}{tr}Preview{/tr}{elseif $parent_com}{tr}Reply to the selected post{/tr}{else}{tr}Post new message{/tr}{/if}
		{else}
			{if $comments_threadId > 0}{tr}Editing comment{/tr}{elseif $parent_com}{tr}Comment on the selected post{/tr}{else}{tr}Post new comment{/tr}{/if}
		{/if}
		</h2>
	</div>

	{if $msgError}<div id="msgError" class="simplebox highlight">
	{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"} 
	{$msgError}</div><br />{/if}

	{if $comment_preview eq 'y'}
	<div class="post_preview">
		{if $forum_mode neq 'y'}<b>{tr}Preview{/tr}</b>{/if}
		<div class="post"><div class="inner"><span class="corners-top"><span></span></span><div class="postbody">
			<div class="postbody-title"><div class="title">{$comments_preview_title}</div></div>
			<div class="content">
				<div class="author"><span class="author_info"><span class="author_post_info">
				{tr}by{/tr} <span class="author_post_info_by">{$user|userlink}</span>
	  			</span></span></div>
				{$comments_preview_data}
	  		</div>
		</div><span class="corners-bottom"><span></span></span></div></div>
	</div>
	{/if}

	<form enctype="multipart/form-data" method="post" action="{$comments_father}" id='editpostform'>
	<input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
	<input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
	<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
	<input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
	<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
	<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
	<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />

	{* Traverse request variables that were set to this page adding them as hidden data *}
	{section name=i loop=$comments_request_data}
	<input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
	{/section}

	<table class="normal">
		<tr>
			<td class="formcolor">
				<label for="comments-title">{tr}Title{/tr}: </label>
				<div class="attention">{tr}Required{/tr}</div>
			</td>
			<td class="formcolor">
				<input type="text" size="50" name="comments_title" id="comments-title" value="{$comment_title|escape}" />
			</td>
		</tr>

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

		{if $prefs.feature_smileys eq 'y'}
		<tr>
			<td class="formcolor"><label>{tr}Smileys{/tr}</label></td>
			<td class="formcolor">{include file="tiki-smileys.tpl" area_name="editpost2"}</td>
		</tr>
		{/if}
                
                {if $quicktags and $prefs.quicktags_over_textarea eq 'y'}
                  <tr>
		    <td class="formcolor"><label>{tr}Quicktags{/tr}</label></td>
                    <td class="formcolor">
                      {include file=tiki-edit_help_tool.tpl area_name='editpost2'}
                    </td>
                  </tr>
                {/if}

		<tr>
			<td class="formcolor">
				<label for="editpost2">{if $forum_mode eq 'y'}{tr}Reply{/tr}{else}{tr}Comment{/tr}{/if}</label>
				<br /><br />
				{include file="textareasize.tpl" area_name='editpost2' formId='editpostform'}
				<br /><br />
                                {if $quicktags and $prefs.quicktags_over_textarea neq 'y'}
				  {include file=tiki-edit_help_tool.tpl area_name='editpost2'}
                                {/if}
			</td>
			<td class="formcolor">
				<textarea id="editpost2" name="comments_data" rows="{$rows}" cols="{$cols}">{if $prefs.feature_forum_replyempty ne 'y' || $edit_reply > 0 || $comment_preview eq 'y'}{$comment_data|escape}{/if}</textarea>
				<input type="hidden" name="rows" value="{$rows}"/>
				<input type="hidden" name="cols" value="{$cols}"/>
			</td>
		</tr>

		{if $forum_mode == "y" and (($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and ($tiki_p_admin_forum eq 'y'  or $forum_info.moderator == $user)) or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y'))}
		<tr>
			<td class="formcolor">{tr}Attach file{/tr}</td>
			<td class="formcolor">
				<input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}" /><input name="userfile1" type="file" />
			</td>
		</tr>
		{/if}

		{if $prefs.feature_contribution eq 'y'}
			{include file="contribution.tpl" in_comment="y"}
		{/if}

		{if $prefs.feature_antibot eq 'y'}
			{include file="antibot.tpl"}
		{/if}

		<tr>
			<td class="formcolor">
			{if $parent_coms}
				{tr}Reply to parent post{/tr}
			{else}
				{if $forum_mode eq 'y'}{tr}Post new reply{/tr}{else}{tr}Post new comment{/tr}{/if}
			{/if}
			</td>

			<td class="formcolor">
			{if empty($user)}
				{tr}Enter your name{/tr}:&nbsp;<input type="text" maxlength="50" size="12" id="anonymous_name" name="anonymous_name" />
			{/if}
				<input type="submit" name="comments_previewComment" value="{tr}Preview{/tr}" {if empty($user)}onclick="setCookie('anonymous_name',document.getElementById('anonymous_name').value);"{/if} />
				<input type="submit" name="comments_postComment" value="{tr}Post{/tr}" {if empty($user)}onclick="setCookie('anonymous_name',document.getElementById('anonymous_name').value);"{/if} />
				{if !empty($user) && $prefs.feature_comments_post_as_anonymous eq 'y'}
				<input type="submit" name="comments_postComment_anonymous" value="{tr}Post as Anonymous{/tr}" />
				{/if}
				{if $forum_mode eq 'y'}
				<input type="button" name="comments_cancelComment" value="{tr}Cancel{/tr}" onclick="hide('{$postclass}');"/>
				{/if}
			</td>
		</tr>
	</table>
	</form>

	<br />
	<table class="normal" id="commentshelp">
		<tr><td class="even">
			<b>{if $forum_mode eq 'y'}{tr}Posting replies{/tr}:{else}{tr}Posting comments{/tr}:{/if}</b><br />
			<br />
			{tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|{tr}Description{/tr}] {tr}for links{/tr}.<br />
			{tr}HTML tags are not allowed inside posts{/tr}.<br />
		</td></tr>
	</table>
	<br />

	{if $forum_mode eq 'y'}
    </div>
	{/if}
		
{/if}
{* End of Post dialog *}

</div>{if $forum_mode neq 'y'}<!-- comzone end -->{/if}
{*</div>  now this tag causes problems instead of fixing (was added earlier to prevent side columns in *litecss themes from not appearing *}
{if empty($user)}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var js_anonymous_name = getCookie('anonymous_name');
if (js_anonymous_name) document.getElementById('anonymous_name').value = js_anonymous_name;
//--><!]]>
</script>
{/if}
