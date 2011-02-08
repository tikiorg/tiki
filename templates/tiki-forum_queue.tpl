
{title help="forums" admpage="forums"}{tr}Message queue for forum{/tr} {$forum_info.name|escape}{/title}

<div class="navbar">
	{button href="tiki-view_forum.php?forumId=$forumId" _text="{tr}Back to forum{/tr}"}
</div>

{if $smarty.request.qId and $form eq 'y'}
<h3>{tr}Edit queued message{/tr}</h3>
<form method="post" action="tiki-forum_queue.php">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="in_reply_to" value="{$msg_info.in_reply_to|escape}" />
<input type="hidden" name="qId" value="{$smarty.request.qId|escape}" />
<table class="formcolor">
<tr>
	<td>{tr}Title{/tr}</td>
	<td>
		<input type="text" name="title" value="{$msg_info.title|escape}" />
	</td>
</tr>
{if $msg_info.parentId > 0}
<tr>
	<td>{tr}Topic{/tr}</td>
	<td>
		<select name="parentId">
			{section name=ix loop=$topics}
			<option value="{$topics[ix].threadId|escape}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title|escape}</option>
			{/section}
		</select>
	</td>
</tr>
{else}
<tr>
	<td>{tr}make this a thread of{/tr}</td>
	<td>
		<select name="parentId">
			<option value="0" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{tr}None, this is a thread message{/tr}</option>
			{section name=ix loop=$topics}
			<option value="{$topics[ix].threadId|escape}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title|escape}</option>
			{/section}
		</select>
	</td>
</tr>
{/if}
{if $msg_info.parentId eq 0 and $forum_info.topic_summary eq 'y'}
	<tr>
		<td>{tr}summary{/tr}</td>
		<td>
			<input type="text" name="summary" value="{$msg_info.summary|escape}" />
		</td>
	</tr>
{/if}
{if $msg_info.parentId eq 0}
<tr>
	<td>{tr}Type{/tr}</td>
	<td>
      <select name="type">
      <option value="n" {if $msg_info.type eq 'n'}selected="selected"{/if}>{tr}Normal{/tr}</option>
      <option value="a" {if $msg_info.type eq 'a'}selected="selected"{/if}>{tr}Announce{/tr}</option>
      <option value="h" {if $msg_info.type eq 'h'}selected="selected"{/if}>{tr}Hot{/tr}</option>
      <option value="s" {if $msg_info.type eq 's'}selected="selected"{/if}>{tr}Sticky{/tr}</option>
      <option value="l" {if $msg_info.type eq 'l'}selected="selected"{/if}>{tr}Locked{/tr}</option>
      </select>
	  {if $forum_info.topic_smileys eq 'y'}
      <select name="topic_smiley">
      <option value="" {if $msg_info.topic_smiley eq ''}selected="selected"{/if}>{tr}no feeling{/tr}</option>
      <option value="icon_frown.gif" {if $msg_info.topic_smiley eq 'icon_frown.gif'}selected="selected"{/if}>{tr}frown{/tr}</option>
      <option value="icon_exclaim.gif" {if $msg_info.topic_smiley eq 'icon_exclaim.gif'}selected="selected"{/if}>{tr}exclaim{/tr}</option>
      <option value="icon_idea.gif" {if $msg_info.topic_smiley eq 'icon_idea.gif'}selected="selected"{/if}>{tr}idea{/tr}</option>
      <option value="icon_mad.gif" {if $msg_info.topic_smiley eq 'icon_mad.gif'}selected="selected"{/if}>{tr}mad{/tr}</option>      
      <option value="icon_neutral.gif" {if $msg_info.topic_smiley eq 'icon_neutral.gif'}selected="selected"{/if}>{tr}neutral{/tr}</option>      
      <option value="icon_question.gif" {if $msg_info.topic_smiley eq 'icon_question.gif'}selected="selected"{/if}>{tr}question{/tr}</option>      
      <option value="icon_sad.gif" {if $msg_info.topic_smiley eq 'icon_sad.gif'}selected="selected"{/if}>{tr}sad{/tr}</option>      
      <option value="icon_smile.gif" {if $msg_info.topic_smiley eq 'icon_smile.gif'}selected="selected"{/if}>{tr}happy{/tr}</option>
      <option value="icon_wink.gif" {if $msg_info.topic_smiley eq 'icon_wink.gif'}selected="selected"{/if}>{tr}wink{/tr}</option>
      </select>
      {/if}
	</td>
</tr>
{/if}
<tr>
	<td>{tr}data{/tr}</td>
	<td>
		{textarea rows="6" cols="60" name="data"}{$msg_info.data}{/textarea}
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
		<input type="submit" name="save" value="{tr}Save{/tr}" />
		<input type="submit" name="saveapp" value="{tr}Save and Approve{/tr}" />
		<input type="submit" name="remove" value="{tr}Remove{/tr}" />
		<input type="submit" name="topicize" value="{tr}convert to topic{/tr}" />
	</td>
</tr>
</table>
</form>
{/if}
<br />
<h3>{tr}List of messages{/tr} ({$cant})</h3>

{* FILTERING FORM *}
{if $items or ($find ne '')}
<form action="tiki-forum_queue.php" method="post">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td>
	<small>{tr}Find{/tr}</small>
	<input size="8" type="text" name="find" value="{$find|escape}" />
	<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</td>
</tr>
</table>	
</form>
{/if}
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-forum_queue.php" method="post">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table class="normal">
<tr>
{if $items}<th>&nbsp;</th>
{/if}
<th>{tr}Message{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr class="{cycle}">
	<td class="checkbox">
	  <input type="checkbox" name="msg[{$items[ix].qId}]" />
	</td>
  
	<td class="text">
		{if $items[ix].parentId > 0}
			[{tr}Topic:{/tr} {$items[ix].topic_title|escape}]
		{else}
			[{tr}New Topic{/tr}]
		{/if}
		<b><a class="link" href="tiki-forum_queue.php?forumId={$forumId}&amp;qId={$items[ix].qId}">{$items[ix].title|escape}</a></b>
		by {$items[ix].user|username} on {$items[ix].timestamp|tiki_short_datetime}
		<br />
		{if $items[ix].parentId eq 0 and $forum_info.topic_summary eq 'y'}
			{if strlen($items[ix].summary) > 0}
				<i>{$items[ix].summary|escape}</i><br />
			{else}
				<i>{tr}no summary{/tr}</i>
			{/if}
		{/if}
		<hr/>
		{$items[ix].parsed}
		  {if count($items[ix].attachments) > 0}
		    <br />
			{section name=iz loop=$items[ix].attachments}
				<a class="link" href="tiki-download_forum_attachment.php?attId={$items[ix].attachments[iz].attId}">
				<img src="img/icons/attachment.gif" width="10" height= "13" alt="{tr}Attachment{/tr}" />
				{$items[ix].attachments[iz].filename} ({$items[ix].attachments[iz].filesize|kbsize})</a>
				<a class="link" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_attachment={$items[ix].attachments[iz].attId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>				
				<br />
			{/section}
  		  {/if}
	</td>
</tr>
{sectionelse}
	{norecords _colspan=2 _text="{tr}No messages queued yet{/tr}"}
{/section}
</table>
{if $items}
<br />
{tr}Perform action with checked:{/tr} 
		<input type="submit" name="rej" value="{tr}Reject{/tr}" />
		<input type="submit" name="app" value="{tr}Approve{/tr}" />
{/if}
</form>
{* END OF LISTING *}

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
