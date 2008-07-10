{*Smarty template*}
<h1><a class="pagetitle" href="messu-archive.php">{tr}Message Archive{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Inter-User Messages" target="tikihelp" class="tikihelp" title="{tr}Message Archive{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=messu-archive.tpl" target="tikihelp" class="tikihelp">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

{include file=tiki-mytiki_bar.tpl}
{include file="messu-nav.tpl"}
{if $prefs.messu_archive_size gt '0'}
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
[{$messu_archive_number} / {$prefs.messu_archive_size}] {tr}messages{/tr}. {if $messu_archive_number eq $prefs.messu_archive_size}{tr}Archive is full!{/tr}{/if}
{/if}
<br /><br />
<form action="messu-archive.php" method="get">
<label for="mess-mailmessages">{tr}Messages{/tr}:</label>
<select name="flags" id="mess-mailmessages">
<option value="isRead_y" {if $flag eq 'isRead' and $flagval eq 'y'}selected="selected"{/if}>{tr}Read{/tr}</option>
<option value="isRead_n" {if $flag eq 'isRead' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unread{/tr}</option>
<option value="isFlagged_y" {if $flag eq 'isFlagged' and $flagval eq 'y'}selected="selected"{/if}>{tr}Flagged{/tr}</option>
<option value="isFlagged_y" {if $flag eq 'isflagged' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unflagged{/tr}</option>
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

<form action="messu-archive.php" method="post" name="form_messu_archive">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="flag" value="{$flag|escape}" />
<input type="hidden" name="flagval" value="{$flagval|escape}" />
<input type="hidden" name="priority" value="{$priority|escape}" />
<input type="submit" name="delete" value="{tr}Delete{/tr}" />
<input type="submit" name="download" value="{tr}Download{/tr}" />
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var CHECKBOX_LIST = [{section name=user loop=$items}'msg[{$items[user].msgId}]'{if not $smarty.section.user.last},{/if}{/section}];
//--><!]]>
</script>

<table class="normal" >
  <tr>
    <td class="heading" ><input type="checkbox" name="checkall" onclick="checkbox_list_check_all('form_messu_archive',CHECKBOX_LIST,this.checked);" /></td>
    <td class="heading" width='18'>&nbsp;</td>
    <td class="heading" ><a class="tableheading" href="messu-archive.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_from_desc'}user_from_asc{else}user_from_desc{/if}">{tr}Sender{/tr}</a></td>
    <td class="heading" ><a class="tableheading" href="messu-archive.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}Subject{/tr}</a></td>
    <td class="heading" ><a class="tableheading" href="messu-archive.php?flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;find={$find}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}Date{/tr}</a></td>
    <td style="text-align:right;" class="heading" >{tr}Size{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=user loop=$items}
  <tr>
    <td class="prio{$items[user].priority}"><input type="checkbox" name="msg[{$items[user].msgId}]" /></td>
    <td class="prio{$items[user].priority}">{if $items[user].isFlagged eq 'y'}{icon _id='flag_blue' alt='{tr}Flagged{/tr}'}{/if}</td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}"><a href="tiki-user_information.php?view_user={$items[user].user_from}">{$items[user].user_from}</a></td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}"><a class="readlink" href="messu-read_archive.php?offset={$offset}&amp;flag={$flag}&amp;priority={$items[user].priority}&amp;flagval={$flagval}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;msgId={$items[user].msgId}">{$items[user].subject}</a></td>
    <td {if $items[user].isRead eq 'n'}style="font-weight:bold"{/if} class="prio{$items[user].priority}">{$items[user].date|tiki_short_datetime}</td><!--date_format:"%d %b %Y [%H:%I]"-->
    <td  style="text-align:right;{if $items[user].isRead eq 'n'}font-weight:bold;{/if}" class="prio{$items[user].priority}">{$items[user].len|kbsize}</td>
  </tr>
  {sectionelse}
  <tr><td colspan="6">{tr}No messages to display{/tr}<td></tr>
  {/section}
</table>
</form>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="messu-archive.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="messu-archive.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;priority={$priority}&amp;flag={$flag}&amp;flagval={$flagval}">{tr}Next{/tr}</a>]
{/if}
</div>
