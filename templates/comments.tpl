{* $Header: /cvsroot/tikiwiki/tiki/templates/comments.tpl,v 1.31 2003-12-21 17:47:30 mose Exp $ *}

<a name="comments"></a>
<br />
{if $comments_show eq 'y'}
<div id="comzoneopen">
{else}
<div id="comzone">
{/if}
  
  {if $tiki_p_read_comments eq 'y'}
    {if $comments_cant gt 0}

 
  {* This section (comment) is only displayed * }
  {* if a reply to it is being composed * }
  {* The $parent_com is only set in this case *}
  {if $parent_com}
  <table class="normal">
  <tr>
  	<td class="odd">
  		<a name="threadId{$parent_com.threadId}"></a>
  		<table >
  			<tr>
			  	<td>
			    	<span class="commentstitle">{$parent_com.title}</span><br />
			  		{tr}by{/tr} {$parent_com.userName} {tr}on{/tr} {$parent_com.commentDate|tiki_long_datetime} [{tr}Score{/tr}:{$parent_com.average|string_format:"%.2f"}]
			  	</td>
			 </tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td class="even">
  		{$parent_com.parsed}
  		<br /><br />
  		{if $parent_com.parentId > 0}
  			[<a class="commentslink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$parent_com.grandFather}">{tr}parent{/tr}</a>]
  		{/if}
  	</td>
  </tr>
  </table>
  {/if}

  {* Conversely, this section is not displayed if a reply is being composed *}
  {if !$parent_com}
 <form method="post" action="{$comments_father}">
  {section name=i loop=$comments_request_data}
  <input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
  {/section}
  <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />    
  <input type="hidden" name="comments_offset" value="0" />
  <table class="normal">
  <caption> {tr}Posted comments{/tr} </caption>
  <tr>
    <td class="heading">{tr}Comments{/tr} 
        <select name="comments_maxComments">
        <option value="10" {if $comments_maxComments eq 10 }selected="selected"{/if}>10</option>
        <option value="20" {if $comments_maxComments eq 20 }selected="selected"{/if}>20</option>
        <option value="30" {if $comments_maxComments eq 30 }selected="selected"{/if}>30</option>
        <option value="999999" {if $comments_maxComments eq 999999 }selected="selected"{/if}>{tr}All{/tr}</option>
        </select>
    </td>
    <td class="heading">{tr}Sort{/tr}
        <select name="comments_sort_mode">
          <option value="commentDate_desc" {if $comments_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
          <option value="commentDate_asc" {if $comments_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
          <option value="points_desc" {if $comments_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
        </select>
    </td>
    <td class="heading">{tr}Threshold{/tr}
        <select name="comments_threshold">
        <option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
        <option value="0.01" {if $comments_threshold eq '0.01'}selected="selected"{/if}>0</option>
        <option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
        <option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
        <option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
        <option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
        </select>
    
    </td>
    <td class="heading">{tr}Search{/tr}
        <input type="text" size="7" name="comments_commentFind" value="{$comments_commentFind|escape}" />
    </td>
    
    <td class="heading"><input type="submit" name="comments_setOptions" value="{tr}set{/tr}" /></td>
    <td class="heading" valign="bottom">
    &nbsp;<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId=0">{tr}Top{/tr}</a>
    </td>
  </tr>
  </table>
  </form>
 
  {section name=com loop=$comments_coms}
  <table class="normal">
  <tr>
  	<td class="odd">
  		<a name="threadId{$comments_coms[com].threadId}"></a>
  		<table >
  			<tr>
			  	<td>
			    	<span class="commentstitle">{$comments_coms[com].title}</span><br />
			  		{tr}by{/tr} {$comments_coms[com].userName} {tr}on{/tr} {$comments_coms[com].commentDate|tiki_long_datetime} [{tr}Score{/tr}:{$comments_coms[com].average|string_format:"%.2f"}]
			  	</td>
			  	<td valign="top" style="text-align:right;" >
			    	{if $tiki_p_vote_comments eq 'y' or $tiki_p_remove_comments eq 'y' or $tiki_p_edit_comments eq 'y'}
			  			{tr}Vote{/tr}: 
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">1</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">2</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">3</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">4</a>
			  				<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">5</a>
			  		{/if}
			  		{if $tiki_p_remove_comments eq 'y'}
			  			(<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[com].threadId}&amp;comments_remove=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}remove{/tr}</a>)
			  		{/if}
			  		{if $tiki_p_edit_comments eq 'y'}
			  			(<a class="link" href="{$comments_complete_father}comments_threadId={$comments_coms[com].threadId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}edit{/tr}</a>)
			  		{/if}
			  	</td>
			 </tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td class="even">
  		{$comments_coms[com].parsed}
  		<br /><br />
  		[<a class="commentslink"
		href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_reply_threadId={$comments_coms[com].threadId}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].threadId}&amp;post_reply=1#threadId{$comments_coms[com].threadId}">{tr}reply to this{/tr}</a>
  		{if $comments_parentId > 0}
  			|<a class="commentslink" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].grandFather}#threadId{$comments_coms[com].parentId}">{tr}parent{/tr}</a>
  		{/if}
  		]
  		{if $comments_coms[com].replies > 0}
  			<br />
  			<ul>
			{assign var="lastlevel" value="0"}
			{assign var="first" value="1"}
  			{section name=rep loop=$comments_coms[com].replies_flat}
				{assign var="level" value=$comments_coms[com].replies_flat[rep].level}
				{if $first}
					{assign var="first" value="0"}
				{else}
					{if $level == $lastlevel}
						</li>
					{elseif $level < $lastlevel}
						{repeat count="$lastlevel = $level"}</li></ul>{/repeat}
					{else}
						<ul>
					{/if}
				{/if}
  				<li><a class="commentshlink"
				href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_coms[com].replies_flat[rep].parentId}#threadId{$comments_coms[com].replies_flat[rep].threadId}">{$comments_coms[com].replies_flat[rep].title}</a>
   				<a class="link"
				href="tiki-user_information.php?view_user={$comments_coms[com].replies_flat[rep].userName}">{tr}by{/tr} {$comments_coms[com].replies_flat[rep].userName} ({tr}Score{/tr}: {$comments_coms[com].replies_flat[rep].points}) {tr}on{/tr} {$comments_coms[com].replies_flat[rep].commentDate|tiki_long_datetime}</a></li>
				{assign var="lastlevel" value=$level}
  			{/section}
  			</li></ul>
  		{/if}
  	</td>
  </tr>
  </table>
  {/section}
  {/if}

<br />
<div align="center">   
    <small>{$comments_below} {tr}Comments below your current threshold{/tr}</small>
  <div class="mini">
  	{if $comments_prev_offset >= 0}
  		[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}prev{/tr}</a>]&nbsp;
  	{/if}
  	{tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  	{if $comments_next_offset >= 0}
  		&nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">{tr}next{/tr}</a>]
  	{/if}
  	{if $direct_pagination eq 'y'}
		<br />
		{section loop=$comments_cant_pages name=foo}
		{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_maxComments}
		<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$selector_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
		{$smarty.section.foo.index_next}</a>&nbsp;
		{/section}
	{/if}
  </div>
  <br />
</div>  

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

  {if $tiki_p_post_comments eq 'y'}
    {if $comments_threadId > 0}
    {tr}Editing comment{/tr}: {$comments_threadId} (<a class="link" href="{$comments_complete_father}comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">{tr}post new comment{/tr}</a>)
    {/if}
    <form method="post" action="{$comments_father}">
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
	<td class="formcolor">{tr}Reply to parent comment{/tr}</td>
      {else}
	<td class="formcolor">{tr}Post new comment{/tr}</td>
      {/if}
      <td class="formcolor">
      <input type="submit" name="comments_previewComment" value="{tr}preview{/tr}"/>
      <input type="submit" name="comments_postComment" value="{tr}post{/tr}"/>
      </td>
    </tr>
    <tr>
      <td class="formcolor">{tr}Title{/tr} <span style="color: red">{tr}Required{/tr}</span></td>
      <td class="formcolor"><input type="text" size="40" name="comments_title" value="{$comment_title|escape}" /></td>
    </tr>

{* Start: Xenfasa adding and testing article ratings in comments here. Not fully functional yet *}
{if $comment_can_rate_article eq 'y'}
    <tr>
      <td class="formcolor">{tr}Rating{/tr} </td>
      <td class="formcolor">
        <select name="comment_rating">
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
      <td class="formcolor">{tr}Smileys{/tr}</td>
      <td class="formcolor">{include file="tiki-smileys.tpl" area_name="editpost"}</td>
    </tr>
    {/if}
    <tr>
      <td class="formcolor">{tr}Comment{/tr}</td>
      <td class="formcolor"><textarea id="editpost" name="comments_data" rows="6" cols="80">{$comment_data|escape}</textarea></td>
    </tr>
    </table>
    </form>
  <br />
  <table class="normal" id="commentshelp">
  <tr><td class="even">
  <b>{tr}Posting comments{/tr}:</b>
  <br />
  <br />
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|description] {tr}for links{/tr}.<br />
  {tr}HTML tags are not allowed inside comments{/tr}.<br />
  </td>
  </tr>
  </table>
  <br />

  {/if}
  {/if}

</div>
