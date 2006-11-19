{* $Header: /cvsroot/tikiwiki/tiki/templates/comments.tpl,v 1.69 2006-11-19 17:12:19 mose Exp $ *}

{if $forum_mode eq 'y'}
<tr><td>
{else}
<a name="comments"></a>
{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}
<div id="comzone" style="display:block;">
{else}
<div id="comzone" style="display:none;">
{/if}
{/if}

{if ($tiki_p_read_comments eq 'y'  and $forum_mode ne 'y') or ($tiki_p_forum_read eq 'y' and $forum_mode eq 'y')}

  {* This section (comment) is only displayed * }
  {* if a reply to it is being composed * }
  {* The $parent_com is only set in this case *}

  {if $comments_cant gt 0}

 <form method="get" action="{$comments_father}" class="comments">
  {section name=i loop=$comments_request_data}
  <input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
  {/section}
  <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />    
  <input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
  <input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
  <input type="hidden" name="comments_offset" value="0" />
	<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
	<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
	<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />
	<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />
	<input type="hidden" name="forumId" value="{$forumId|escape}" />


		{if $tiki_p_admin_forum eq 'y' and $forum_mode eq 'y'}
<table class="normal">
	<tr>
		<td colspan="3" class="heading">{tr}Moderator actions{/tr}</td>
	</tr>
	<tr>
		<td class="odd">
			<input type="submit" name="delsel" value="{tr}delete selected{/tr}" />
		</td>
		<td class="odd">
			{tr}Move to topic:{/tr}
			<select name="moveto">
			{section name=ix loop=$topics}
				{if $topics[ix].threadId ne $comments_parentId}
					<option value="{$topics[ix].threadId|escape}">{$topics[ix].title}</option>
				{/if}
			{/section}
			</select>
			<input type="submit" name="movesel" value="{tr}move{/tr}" />
		</td>
		<td style="text-align:right;" class="odd">
			{if $reported > 0}
				<small><a class="link" href="tiki-forums_reported.php?forumId={$forumId}">{tr}reported:{/tr}{$reported}</a> | </small>
			{/if}
			<small><a class="link" href="tiki-forum_queue.php?forumId={$forumId}">{tr}queued:{/tr}{$queued}</a></small>
		</td>

	</tr>
</table>
		{/if}

<h2> {tr}Posted messages{/tr} </h2>
  <table class="normal">
  <tr>
    <td class="heading"><label for="comments-maxcomm">{tr}Messages{/tr} </label>
        <select name="comments_maxComments" id="comments-maxcomm">
        <option value="10" {if $comments_maxComments eq 10 }selected="selected"{/if}>10</option>
        <option value="20" {if $comments_maxComments eq 20 }selected="selected"{/if}>20</option>
        <option value="30" {if $comments_maxComments eq 30 }selected="selected"{/if}>30</option>
        <option value="999999" {if $comments_maxComments eq 999999 }selected="selected"{/if}>{tr}All{/tr}</option>
        </select>
    </td>
    <td class="heading"><label for="comments-style">{tr}Style{/tr}</label>
        <select name="comments_style" id="comments-style">
          <option value="commentStyle_plain" {if $comments_style eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
          <option value="commentStyle_threaded" {if $comments_style eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
          <option value="commentStyle_headers" {if $comments_style eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
        </select>
    </td>
    <td class="heading"><label for="comments-sort">{tr}Sort{/tr}</label>
        <select name="comments_sort_mode" id="comments-sort">
          <option value="commentDate_desc" {if $comments_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
          <option value="commentDate_asc" {if $comments_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
          <option value="points_desc" {if $comments_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
        </select>
    </td>
    <td class="heading"><label for="comments-thresh">{tr}Threshold{/tr}</label>
        <select name="comments_threshold" id="comments-thresh">
        <option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
        <option value="0.01" {if $comments_threshold eq '0.01'}selected="selected"{/if}>0</option>
        <option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
        <option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
        <option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
        <option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
        </select>
    
    </td>
    <td class="heading"><label for="comments-search">{tr}Find{/tr}</label>
        <input type="text" size="7" name="comments_commentFind" id="comments-search" value="{$comments_commentFind|escape}" />
    </td>

    <td class="heading"><input type="submit" name="comments_setOptions" value="{tr}set{/tr}" /></td>
    <td class="heading" style="text-align: center; vertical-align: middle">
    	<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}{$comments_sort_mode_param}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}&amp;comments_parentId=0">{tr}Top{/tr}</a>
    </td>

{if $forum_mode ne 'y'}
    <td class="heading" style="text-align: center; vertical-align: middle">
		<a class="link" href="{$comments_complete_father}comzone=hide">{tr}Hide all{/tr}</a>
    </td>
{/if}

  </tr>
  </table>
 
    <table class="normal">
{if $comments_style != 'commentStyle_plain'}
<tr>
<td class="odd">
{/if}
  {section name=rep loop=$comments_coms}
  {include file="comment.tpl"  comment=$comments_coms[rep]}
{if $comments_style != 'commentStyle_plain'}<br />{/if}
  {/section}
</td>
</tr>
    </table>
</form>

<br />
<div style="text-align: center">
  {if $comments_threshold ne 0}<small>{$comments_below}&nbsp;{if $comments_below eq 1}{tr}reply{/tr}{else}{tr}replies{/tr}{/if} {tr}below your current threshold{/tr}</small>{/if}
  <div class="mini">
  	{if $comments_prev_offset >= 0}
  		[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_prev_offset}{$comments_sort_mode_param}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">{tr}prev{/tr}</a>]&nbsp;
  	{/if}
  	{tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  	{if $comments_next_offset >= 0}
  		&nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$comments_next_offset}{$comments_sort_mode_param}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">{tr}next{/tr}</a>]
  	{/if}
  	{if $direct_pagination eq 'y'}
		<br />
		{section loop=$comments_cant_pages name=foo}
		{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_maxComments}
		<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_parentId={$comments_parentId}&amp;comments_offset={$selector_offset}{$comments_sort_mode_param}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">
		{$smarty.section.foo.index_next}</a>&nbsp;
		{/section}
	{/if}
  </div>
  <br />
</div>  

  {/if}
 {/if} {* end read comment *}

{* Post dialog *}	
{if ($tiki_p_forum_post eq 'y' and $forum_mode eq 'y') or ($tiki_p_post_comments eq 'y' and $forum_mode ne 'y')}
    {if $forum_mode eq 'y'}
	{if $post_reply > 0 || $edit_reply > 0 || $comment_preview} {* posting a reply or editing or previwing a reply: show form *}
		<div id='{$postclass}open' class="threadpost">
	{else}
		<input type="button" name="comments_postComment" value="{tr}new reply{/tr}" onclick="flip('{$postclass}');"/>
		<div id='{$postclass}' class="threadpost">
	{/if}
    {/if}

	<a name="form"></a>
	<div>
    <h2 style="text-align: left">
    {if $forum_mode eq 'y'}
    {if $comments_threadId > 0}{tr}Editing reply{/tr}{elseif $parent_com}{tr}Reply to the selected post{/tr}{else}{tr}Post new message{/tr}{/if}
    {else}
    {if $comments_threadId > 0}{tr}Editing comment{/tr}{elseif $parent_com}{tr}Comment on the selected post{/tr}{else}{tr}Post new comment{/tr}{/if}
    {/if}
    </h2>
	</div>

{if $msgError}
	<a name="msgError"></a><div class="simplebox highlight">{$msgError}</div>
{/if}

  {if $comment_preview eq 'y'}
  <b>{tr}Preview{/tr}</b>
  <table class="normal">
  	<tr>
  		<td class="odd">
  			<span class="commentstitle">{$comments_preview_title}</span><br />
  			{tr}by{/tr} {$user|userlink}
  		</td>
  	</tr>
  	<tr>
  		<td class="even">
  			{$comments_preview_data}
  		</td>
  	</tr>
  </table>
  {/if}


    <form enctype="multipart/form-data" method="post" action="{$comments_father}" id='editpostform'>
    <input type="hidden" name="comments_reply_threadId" value="{$comments_reply_threadId|escape}" />    
    <input type="hidden" name="comments_grandParentId" value="{$comments_grandParentId|escape}" />    
    <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode|escape}" />
    {* Traverse request variables that were set to this page adding them as hidden data *}
    {section name=i loop=$comments_request_data}
    <input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
    {/section}
    <table class="normal">
    <tr>
      {if $parent_coms}
	<td class="formcolor">{tr}Reply to parent post{/tr}</td>
      {else}
	<td class="formcolor">
		{if $forum_mode eq 'y'}
		{tr}Post new reply{/tr}
		{else}
		{tr}Post new comment{/tr}
		{/if}
	</td>
      {/if}
      <td class="formcolor">
      <input type="submit" name="comments_previewComment" value="{tr}preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}post{/tr}"/>
    {if $forum_mode eq 'y'}
    <input type="button" name="comments_cancelComment" value="{tr}cancel{/tr}" onclick="hide('{$postclass}');"/>
    {/if}
      </td>
    </tr>
{if $feature_contribution eq 'y'}
	{include file="contribution.tpl"}
{/if}
    <tr>
      <td class="formcolor"><label for="comments-title">{tr}Title{/tr}: </label><div class="attention">{tr}Required{/tr}</div></td>
      <td class="formcolor"><input type="text" size="40" name="comments_title" id="comments-title" value="{$comment_title|escape}" /></td>
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

    {if $feature_smileys eq 'y'}
    <tr>
      <td class="formcolor"><label>{tr}Smileys{/tr}</label></td>
      <td class="formcolor">{include file="tiki-smileys.tpl" area_name="editpost2"}</td>
    </tr>
    {/if}
    <tr>
      <td class="formcolor"><label for="editpost2">
      	{if $forum_mode eq 'y'}
      	{tr}Reply{/tr}
      	{else}
      	{tr}Comment{/tr}
      	{/if}
      </label><br /><br />{include file="textareasize.tpl" area_name='editpost2' formId='editpostform'}<br /><br />{if $quicktags}{include file=tiki-edit_help_tool.tpl area_name='editpost2'}{/if}</td>
      <td class="formcolor"><textarea id="editpost2" name="comments_data" rows="{$rows}" cols="{$cols}">{$comment_data|escape}</textarea>
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
    </table>
    </form>
  <br />
  <table class="normal" id="commentshelp">
  <tr><td class="even">
  <b>
  	{if $forum_mode eq 'y'}
  	{tr}Posting replies{/tr}:
  	{else}
  	{tr}Posting comments{/tr}:
  	{/if}
  </b>
  <br />
  <br />
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|{tr}description{/tr}] {tr}for links{/tr}.<br />
  {tr}HTML tags are not allowed inside posts{/tr}.<br />
  </td>
  </tr>
  </table>
  <br />

	{if $forum_mode eq 'y'}
    </div>
	{/if}
		
{/if}
{* End of Post dialog *}


{if $forum_mode eq 'y'}
    </td></tr>
{else}
    </div><!-- comzone end -->
{/if}
