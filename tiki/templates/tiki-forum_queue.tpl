{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-forum_queue.php?forumId={$forumId}">{tr}Message queue for{/tr}: {$forum_info.name}</a>
<br/><br/>
<a class="link" href="tiki-view_forum.php?forumId={$forumId}">{tr}back to forum{/tr}</a>
{if $smarty.request.qId and $form eq 'y'}
<h3>{tr}Edit queued message{/tr}</h3>
<form method="post" action="tiki-forum_queue.php">
<input type="hidden" name="forumId" value="{$forumId}" />
<input type="hidden" name="qId" value="{$smarty.request.qId}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}title{/tr}</td>
	<td class="formcolor">
		<input type="text" name="title" value="{$msg_info.title}" />
	</td>
</tr>
{if $msg_info.parentId > 0}
<tr>
	<td class="formcolor">{tr}topic{/tr}</td>
	<td class="formcolor">
		<select name="parentId">
			{section name=ix loop=$topics}
			<option value="{$topics[ix].threadId}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title}</option>
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
			<option value="{$topics[ix].threadId}" {if $topics[ix].threadId eq $msg_info.parentId}selected="selected"{/if}>{$topics[ix].title}</option>
			{/section}
		</select>
	</td>
</tr>
{/if}
{if $msg_info.parentId eq 0 and $forum_info.topic_summary eq 'y'}
	<tr>
		<td class="formcolor">{tr}summary{/tr}</td>
		<td class="formcolor">
			<input type="text" name="summary" value="{$msg_info.summary}" />
		</td>
	</tr>
{/if}
{if $msg_info.parentId eq 0}
<tr>
	<td class="formcolor">{tr}type{/tr}</td>
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
		<textarea rows="6" cols="60" name="data">{$msg_info.data}</textarea>
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">
		<input type="submit" name="save" value="{tr}save{/tr}" />
		<input type="submit" name="saveapp" value="{tr}save and approve{/tr}" />
		<input type="submit" name="remove" value="{tr}remove{/tr}" />
		<input type="submit" name="topicize" value="{tr}convert to topic{/tr}" />
	</td>
</tr>
</table>
</form>
{/if}

<h3>{tr}List of messages{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-forum_queue.php" method="post">
<input type="hidden" name="forumId" value="{$forumId}" />
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table>
<tr>
<td>
	<small>{tr}find{/tr}</small>
	<input size="8" type="text" name="find" value="{$find}" />
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-forum_queue.php" method="post">
<input type="hidden" name="forumId" value="{$forumId}" />
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="find" value="{$find}" />
<table class="normal">
<tr>
<td width="2%" class="heading" >&nbsp;</td>
<td class="heading" >{tr}message{/tr}</td>
</td>
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
			[{tr}new topic{/tr}]
		{/if}
		<b><a class="link" href="tiki-forum_queue.php?forumId={$forumId}&amp;qId={$items[ix].qId}">{$items[ix].title}</a></b>
		by {$items[ix].user} on {$items[ix].timestamp|tiki_short_datetime}
		<br/>
		{if $items[ix].parentId eq 0 and $forum_info.topic_summary eq 'y'}
			{if strlen($items[ix].summary) > 0}
				<i>{$items[ix].summary}</i><br/>
			{else}
				<i>{tr}no summary{/tr}</i>
			{/if}
		{/if}
		<hr/>
		{$items[ix].parsed}
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="26">
	{tr}No messages queued yet{/tr}
	</td>
</tr>	
{/section}
<tr>
	<td style='text-align:center;' class="heading" colspan='16'>
		<input type="submit" name="rej" value="{tr}reject{/tr}" />
		<input type="submit" name="app" value="{tr}approve{/tr}" />
	</td>
</tr>
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-forum_queue.php?forumId={$forumId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}