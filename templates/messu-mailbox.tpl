{*Smarty template*}
<h1><a class="pagetitle" href="messu-mailbox.php">{tr}Messages{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Inter-User Messages" target="tikihelp" class="tikihelp" title="{tr}Messages{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=messu-mailbox.tpl" target="tikihelp" class="tikihelp">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>
{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
{/if}
{include file="messu-nav.tpl"}
{if $prefs.messu_mailbox_size gt '0'}
<br />
<table border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table border='0' height='20' cellpadding='0' cellspacing='0'
			       width='200' style='background-color:#666666;'>
				<tr>
					<td style='background-color:red;' width='{$cellsize}'>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td><small>{$percentage}%</small></td>
	</tr>
</table>
[{$messu_mailbox_number} / {$prefs.messu_mailbox_size}] {tr}messages{/tr}. {if $messu_mailbox_number ge $prefs.messu_mailbox_size}{tr}Mailbox is full! Delete or archive some messages if you want to receive more messages.{/tr}{/if}
{/if}
<br /><br />
<form action="messu-mailbox.php" method="get">
<label for="mess-mailmessages">{tr}Messages{/tr}:</label>
<select name="flags" id="mess-mailmessages">
<option value="isRead_y" {if $flag eq 'isRead' and $flagval eq 'y'}selected="selected"{/if}>{tr}Read{/tr}</option>
<option value="isRead_n" {if $flag eq 'isRead' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unread{/tr}</option>
<option value="isFlagged_y" {if $flag eq 'isFlagged' and $flagval eq 'y'}selected="selected"{/if}>{tr}Flagged{/tr}</option>
<option value="isFlagged_n" {if $flag eq 'isflagged' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unflagged{/tr}</option>
<option value="" {if $flag eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
</select>
<label for="mess-mailprio">{tr}Priority{/tr}:</label>
<select name="priority" id="mess-mailprio">
<option value="" {if $priority eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
<option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
</select>
<label for="mess-mailcont">{tr}Containing{/tr}:</label>
<input type="text" name="find" id="mess-mailcont" value="{$find|escape}" />
<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</form>
<br />

<form action="messu-mailbox.php" method="post" name="form_messu_mailbox">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="flag" value="{$flag|escape}" />
<input type="hidden" name="flagval" value="{$flagval|escape}" />
<input type="hidden" name="priority" value="{$priority|escape}" />
<input type="submit" name="delete" value="{tr}Delete{/tr}" />
<input type="submit" name="archive" value="{tr}move to archive{/tr}" />
<input type="submit" name="download" value="{tr}Download{/tr}" />
<select name="action">
<option value="isRead_y">{tr}Mark as read{/tr}</option>
<option value="isRead_n">{tr}Mark as unread{/tr}</option>
<option value="isFlagged_y">{tr}Mark as flagged{/tr}</option>
<option value="isFlagged_n">{tr}Mark as unflagged{/tr}</option>
</select>
<script type="text/javascript">
var CHECKBOX_LIST = [{section name=user loop=$items}'msg[{$items[user].msgId}]'{if not $smarty.section.user.last},{/if}{/section}];
</script>
<input type="submit" name="mark" value="{tr}mark{/tr}" />
<table class="normal" >
  <tr>
    <td class="heading" ><input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_messu_mailbox',CHECKBOX_LIST,this.checked);" /></td>
    <td class="heading" width='18' >&nbsp;</td>
    <td class="heading" ><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_from_desc'}user_from_asc{else}user_from_desc{/if}">{tr}sender{/tr}</a></td>
    <td class="heading" ><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></td>
    <td class="heading" ><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}date{/tr}</a></td>
    <td class="heading" >{tr}is reply to{/tr}</td>
    <td style="text-align:right;" class="heading" >{tr}Size{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=user loop=$items}
  <tr>
    <td class="prio{$items[user].priority}"><input type="checkbox" name="msg[{$items[user].msgId}]" /></td>
    <td class="prio{$items[user].priority}">{if $items[user].isFlagged eq 'y'}{icon _id='flag_blue' alt='{tr}flagged{/tr}'}{/if}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].user_from|userlink}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}"><a class="readlink" href="messu-read.php?offset={$offset}&amp;flag={$flag}&amp;priority={$items[user].priority}&amp;flagval={$flagval}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;msgId={$items[user].msgId}">{$items[user].subject}</a></td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].date|tiki_short_datetime}</td><!--date_format:"%d %b %Y [%H:%I]"-->
		<td class="prio{$items[user].priority}">
		{if $items[user].replyto_hash eq ""}&nbsp;{else}
			<a class="readlink" href="messu-mailbox.php?origto={$items[user].replyto_hash}">
		    {icon _id='email_go' alt='{tr}find replied message{/tr}'}
			</a>
		{/if}
		</td>
    <td  style="text-align:right;{if $items[user].isRead eq 'n'}font-weight:bold;{/if}" class="prio{$items[user].priority}">{$items[user].len|kbsize}</td>
  </tr>
  {sectionelse}
  <tr><td colspan="7" class="odd">{tr}No messages to display{/tr}<td></tr>
  {/section}
</table>
</form>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="messu-mailbox.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="messu-mailbox.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}Next{/tr}</a>]
{/if}
</div>
