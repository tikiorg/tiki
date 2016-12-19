{title help="Inter-User Messages" admpage="messages"}{tr}Sent Messages{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}
{if $prefs.messu_sent_size gt '0'}

	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="{$cellsize}" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;">
			{$percentage}%
		</div>
	</div>
	<div class="margin-bottom-md">
[{$messu_sent_number} / {$prefs.messu_sent_size}] {tr}messages{/tr}. {if $messu_sent_number ge $prefs.messu_sent_size}{tr}Sent box is full. Archive or delete some sent messages first if you want to send more messages.{/tr}{/if}
</div>
{/if}



<form class="form-inline margin-bottom-md" action="messu-sent.php" method="get">
	<div class="form-group">
	<label for="mess-mailmessages">{tr}Messages:{/tr}</label>
	<select name="flags" id="mess-mailmessages" class="form-control">
		<option value="isReplied_y" {if $flag eq 'isRead' and $flagval eq 'y'}selected="selected"{/if}>{tr}Replied{/tr}</option>
		<option value="isReplied_n" {if $flag eq 'isRead' and $flagval eq 'n'}selected="selected"{/if}>{tr}Not replied{/tr}</option>
		<option value="" {if $flag eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
	</select>
	</div>
	<div class="form-group">
	<label for="mess-mailprio">{tr}Priority:{/tr}</label>
	<select name="priority" id="mess-mailprio" class="form-control">
		<option value="" {if $priority eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
		<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
		<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
		<option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
		<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
		<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
	</select>
		</div>
	<div class="form-group">
	<label for="mess-mailcont">{tr}Containing:{/tr}</label>
	<input type="text" name="find" id="mess-mailcont" value="{$find|escape}" class="form-control">
		</div>
	<input type="submit" class="btn btn-default btn-sm" name="filter" value="{tr}Filter{/tr}">
</form>

<form action="messu-sent.php" method="post" name="form_messu_sent">
	<div class="form-group">
	<input type="hidden" name="offset" value="{$offset|escape}">
	<input type="hidden" name="find" value="{$find|escape}">
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
	<input type="hidden" name="flag" value="{$flag|escape}">
	<input type="hidden" name="flagval" value="{$flagval|escape}">
	<input type="hidden" name="priority" value="{$priority|escape}">
	<input type="submit" class="btn btn-default btn-sm" name="delete" value="{tr}Delete{/tr}">
	<input type="submit" class="btn btn-default btn-sm" name="archive" value="{tr}Move to archive{/tr}">
	<input type="submit" class="btn btn-default btn-sm" name="download" value="{tr}Download{/tr}">
	</div>
{jq notonready=true}
var CHECKBOX_LIST = [{{section name=user loop=$items}'msg[{$items[user].msgId}]'{if not $smarty.section.user.last},{/if}{/section}}];
{/jq}
	<div class="table-responsive">
	<table class="table" >
		<tr>
			<th><input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_messu_sent',CHECKBOX_LIST,this.checked);"></th>
			<th style="width:18px">&nbsp;</th>
			<th><a href="messu-sent.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_to_desc'}user_to_asc{else}user_to_desc{/if}">{tr}Recipient{/tr}</a></th>
			<th><a href="messu-sent.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}Subject{/tr}</a></th>
			<th><a href="messu-sent.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}Date{/tr}</a></th>
			<th><a href="messu-sent.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isReplied_desc'}isReplied_asc{else}isReplied_desc{/if}">{tr}Replies{/tr}</a></th>
			<th style="text-align:right;">{tr}Size{/tr}</th>
		</tr>

		{section name=user loop=$items}
			<tr>
				<td class="prio{$items[user].priority}"><input type="checkbox" name="msg[{$items[user].msgId}]"></td>
				<td class="prio{$items[user].priority}">{if $items[user].isFlagged eq 'y'}{icon name='flag' alt="{tr}Flagged{/tr}"}{/if}</td>
				<td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].user_to|username}</td>
				<td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}"><a class="readlink" href="messu-read_sent.php?offset={$offset}&amp;flag={$flag}&amp;priority={$items[user].priority}&amp;flagval={$flagval}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;msgId={$items[user].msgId}">{$items[user].subject|escape}</a></td>
				<td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].date|tiki_short_datetime}</td><!--date_format:"%d %b %Y [%H:%I]"-->

				<td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">
					{if $items[user].isReplied eq 'n'}{tr}No{/tr}{else}
						<a class="readlink" href="messu-mailbox.php?replyto={$items[user].hash}">
							{icon name='envelope' alt="{tr}Replied{/tr}"}
						</a>
						&nbsp;
						<a href="tiki-user_information.php?view_user={$items[user].user_from}">{$items[user].user_from}</a>
					{/if}
				</td>
				<td style="text-align:right;{if $items[user].isRead eq 'n'}font-weight:bold;{/if}" class="prio{$items[user].priority}">{$items[user].len|kbsize}</td>
			</tr>
		{sectionelse}
			<tr><td colspan="6">{tr}No messages to display{/tr}<td></tr>
		{/section}
	</table>
	</div>
</form>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
