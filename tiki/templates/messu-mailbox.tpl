<a class="pagetitle" href="messu-mailbox.php">{tr}Messages{/tr}</a><br/><br/>
{*Smarty template*}
{include file="messu-nav.tpl"}
<br/><br/>

<form action="messu-mailbox.php" method="get">
{tr}Messages{/tr}:
<select name="flags">
<option value="isRead_y" {if $flag eq 'isRead' and $flagval eq 'y'}selected="selected"{/if}>{tr}Read{/tr}</option>
<option value="isRead_n" {if $flag eq 'isRead' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unread{/tr}</option>
<option value="isFlagged_y" {if $flag eq 'isFlagged' and $flagval eq 'y'}selected="selected"{/if}>{tr}Flagged{/tr}</option>
<option value="isFlagged_y" {if $flag eq 'isflagged' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unflagged{/tr}</option>
<option value="" {if $flag eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
</select>
{tr}Priority{/tr}:
<select name="priority">
<option value="" {if $priority eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
<option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
</select>
{tr}Containing{/tr}:
<input type="text" name="find" value="{$find}" />
<input type="submit" name="filter" value="filter" />
</form>
<br/>

<form action="messu-mailbox.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="flag" value="{$flag}" />
<input type="hidden" name="flagval" value="{$flagval}" />
<input type="hidden" name="priority" value="{$priority}" />
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<select name="action">
<option value="isRead_n">{tr}Mark as unread{/tr}</option>
<option value="isRead_y">{tr}Mark as read{/tr}</option>
<option value="isFlagged_n">{tr}Mark as unflagged{/tr}</option>
<option value="isFlagged_y">{tr}Mark as flagged{/tr}</option>
</select>
<input type="submit" name="mark" value="{tr}mark{/tr}" />
<table class="normal" width="100%">
  <tr>
    <td class="heading" width="3%">&nbsp;</td>
    <td class="heading" width="4%">&nbsp;</td>
    <td class="heading" width="15%"><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_from_desc'}user_from_asc{else}user_from_desc{/if}">{tr}from{/tr}</a></td>
    <td class="heading" width="40%"><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></td>
    <td class="heading" width="30%"><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}date{/tr}</a></td>
    <td class="heading" width="8%"><a class="tableheading" href="messu-mailbox.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a></td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=user loop=$items}
  <tr>
    <td class="prio{$items[user].priority}"><input type="checkbox" name="msg[{$items[user].msgId}]" /></td>
    <td class="prio{$items[user].priority}">{if $items[user].isFlagged eq 'y'}<img alt="flagged" src="img/flagged.gif" />{/if}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].user_from}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}"><a class="readlink" href="messu-read.php?offset={$offset}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;msgId={$items[user].msgId}">{$items[user].subject}</a></td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].date|date_format:"%a %b %Y [%H:%I]"}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].len|kbsize}</td>
  </tr>
  {sectionelse}
  <tr><td colspan="6">{tr}No messages to display{/tr}<td></tr>
  {/section}
</table>
</form>

<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="messu-mailbox.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="messu-mailbox.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

