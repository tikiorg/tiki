{* $Header: /cvsroot/tikiwiki/tiki/templates/comments.tpl,v 1.44 2004-06-15 05:06:30 rlpowell Exp $ *}

<a name="comments"></a>
<br />
{if $comments_show eq 'y'}
<div id="comzoneopen">
{else}
<div id="comzone">
{/if}
  
  {if $tiki_p_read_comments eq 'y'}
    {if $comments_cant gt 0}
 
 <form method="post" action="{$comments_father}">
  {section name=i loop=$comments_request_data}
  <input type="hidden" name="{$comments_request_data[i].name|escape}" value="{$comments_request_data[i].value|escape}" />
  {/section}
  <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />    
  <input type="hidden" name="comments_offset" value="0" />
  <table class="normal">
  <caption> {tr}Posted comments{/tr} </caption>
  <tr>
    <td class="heading"><label for="comments-maxcomm">{tr}Comments{/tr} </label>
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
          <option value="commentsStyle_headers" {if $comments_style eq 'commentsStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
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
    <td class="heading" valign="bottom">
    &nbsp;<a class="link" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}&amp;comments_parentId=0">{tr}Top{/tr}</a>
    </td>
  </tr>
  </table>
  </form>
 
    <table class="normal">
<tr>
<td class="odd">
  {section name=rep loop=$comments_coms}
  {include file="comment.tpl"  comment=$comments_coms[rep]}
  {/section}
</td>
</tr>
    </table>

<br />
<div align="center">   
    <small>{$comments_below}&nbsp;{tr}Comments below your current threshold{/tr}</small>
  <div class="mini">
  	{if $comments_prev_offset >= 0}
  		[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">{tr}prev{/tr}</a>]&nbsp;
  	{/if}
  	{tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
  	{if $comments_next_offset >= 0}
  		&nbsp;[<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">{tr}next{/tr}</a>]
  	{/if}
  	{if $direct_pagination eq 'y'}
		<br />
		{section loop=$comments_cant_pages name=foo}
		{assign var=selector_offset value=$smarty.section.foo.index|times:$comments_maxComments}
		<a class="prevnext" href="{$comments_complete_father}comments_threshold={$comments_threshold}&amp;comments_offset={$selector_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_style={$comments_style}">
		{$smarty.section.foo.index_next}</a>&nbsp;
		{/section}
	{/if}
  </div>
  <br />
</div>  

  {/if}

  {* This section (comment) is only displayed * }
  {* if a reply to it is being composed * }
  {* The $parent_com is only set in this case *}
  {if $tiki_p_post_comments eq 'y'}
 <a name="form"></a>
<table style="width:100%;"><tr><td>
    <h2>
    {if $comments_threadId > 0}{tr}Editing comment{/tr}{elseif $parent_com}{tr}Comment the selected comment{/tr}{else}{tr}Post new comment{/tr}{/if}
    </h2>
    {if $comments_threadId > 0 || $parent_com}
</td><td align="right">
<a class="linkbut" href="{$comments_complete_father}comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;comments_style={$comments_style}">{tr}post new comment{/tr}</a>
    {/if}
</td></tr></table>

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


    <form method="post" action="{$comments_father}" id='editpostform'>
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
      <td class="formcolor">{include file="tiki-smileys.tpl" area_name="editpost"}</td>
    </tr>
    {/if}
    <tr>
      <td class="formcolor"><label for="editpost">{tr}Comment{/tr}</label><br /><br />{include file="textareasize.tpl" area_name='editpost' formId='editpostform'}<br /><br /></td>
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
  {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|{tr}description{/tr}] {tr}for links{/tr}.<br />
  {tr}HTML tags are not allowed inside comments{/tr}.<br />
  </td>
  </tr>
  </table>
  <br />

  {/if}
  {/if}
</div>
