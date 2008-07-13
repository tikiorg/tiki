{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-forum_queue.php?forumId={$forumId}">{tr}Message queue for{/tr}: {$forum_info.name}</a></h1>
<a class="link" href="tiki-view_forum.php?forumId={$forumId}">{tr}back to forum{/tr}</a>
{if $smarty.request.qId and $form eq 'y'}
<h3>{tr}Edit queued message{/tr}</h3>
<form method="post" action="tiki-forum_queue.php">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="in_reply_to" value="{$msg_info.in_reply_to|escape}" />
<input type="hidden" name="qId" value="{$smarty.request.qId|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}Title{/tr}</td>
	<td class="formcolor">
		<input type="text" name="title" value="{$msg_info.title|escape}" />
	</td>
</tr>
{if $msg_info.parentId > 0}
<tr>
	<td class="formcolor">{tr}Topic{/tr}</td>
	<td class="formcolor">
		<select name="parentId">
			{section name=ix loop=$topics}
			<option value="{$topics[ix].threadId|escape}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title}</option>
			{/section}
		</select>
	</td>
</tr>
{else}
<tr>
	<td class="formcolor">{tr}make this a thread of{/tr}</td>
	<td class="formcolor">
		<select name="parentId">
			<option value="0" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{tr}None, this is a thread message{/tr}</option>
			{section name=ix loop=$topics}
			<option value="{$topics[ix].threadId|escape}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title}</option>
			{/section}
		</select>
	</td>
</tr>
{/if}
{if $msg_info.parentId eq 0 and $forum_info.topic_summary eq 'y'}
	<tr>
		<td class="formcolor">{tr}summary{/tr}</td>
		<td class="formcolor">
			<input type="text" name="summary" value="{$msg_info.summary|escape}" />
		</td>
	</tr>
{/if}
{if $msg_info.parentId eq 0}
<tr>
	<td class="formcolor">{tr}Type{/tr}</td>
	<td class="formcolor">
      <select name="type">
      <option value="n" {if $msg_info.type eq 'n'}selected="selected"{/if}>{tr}normal{/tr}</option>
      <option value="a" {if $msg_info.type eq 'a'}selected="selected"{/if}>{tr}announce{/tr}</option>
      <option value="h" {if $msg_info.type eq 'h'}selected="selected"{/if}>{tr}hot{/tr}</option>
      <option value="s" {if $msg_info.type eq 's'}selected="selected"{/if}>{tr}sticky{/tr}</option>
      <option value="l" {if $msg_info.type eq 'l'}selected="selected"{/if}>{tr}locked{/tr}</option>
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
	<td class="formcolor">{tr}data{/tr}</td>
	<td class="formcolor">
		<textarea rows="6" cols="60" name="data">{$msg_info.data|escape}</textarea>
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">
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
{if $items}<th class="heading" >&nbsp;</th>
{/if}
<th class="heading" >{tr}Message{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:center;" class="{cycle advance=false}">
	  <input type="checkbox" name="msg[{$items[ix].qId}]" />
	</td>
  
	<td class="{cycle}" style="text-align:left;">
		{if $items[ix].parentId > 0}
			[{tr}Topic{/tr}: {$items[ix].topic_title}]
		{else}
			[{tr}New Topic{/tr}]
		{/if}
		<b><a class="link" href="tiki-forum_queue.php?forumId={$forumId}&amp;qId={$items[ix].qId}">{$items[ix].title}</a></b>
		by {$items[ix].user} on {$items[ix].timestamp|tiki_short_datetime}
		<br />
		{if $items[ix].parentId eq 0 and $forum_info.topic_summary eq 'y'}
			{if strlen($items[ix].summary) > 0}
				<i>{$items[ix].summary}</i><br />
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
				<img src="img/icons/attachment.gif" border="0" width="10" height= "13" alt='{tr}Attachment{/tr}' />
				{$items[ix].attachments[iz].filename} ({$items[ix].attachments[iz].filesize|kbsize})</a>
				<a class="link" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_attachment={$items[ix].attachments[iz].attId}">[{tr}Del{/tr}]</a>					
				<br />
			{/section}
  		  {/if}

		
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="2">
	{tr}No messages queued yet{/tr}
	</td>
</tr>	
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

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}
