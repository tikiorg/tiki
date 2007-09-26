{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_irc.tpl,v 1.9 2007-09-26 13:37:54 jyhem Exp $
Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
All Rights Reserved. See copyright.txt for details and a complete list of authors.
Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
*}

<h1><a class="pagetitle" href="tiki-view_irc.php">
{tr}IRC log{/tr} {$irc_log_channel} {$irc_log_time|tiki_long_date}
</a>
</h1>
<div>
<form method="get" action="tiki-view_irc.php">
<table>
<tr>
   <td valign='bottom'>{tr}Select{/tr}</td>
   <td class="findtable" valign='top'>
        <select name="log" onchange="this.form.submit()">
          {html_options options="$irc_log_options" selected="$irc_log_selected"}
        </select>
       <input type="submit" value="{tr}Display{/tr}" />
   </td>

   <td valign='bottom'>{tr}Filter{/tr}</td>
   <td class="findtable" valign='top'>
        <input type="text" name="filter" value="{$filter}" />
   </td>

   <td valign='bottom'>{tr}Show All{/tr}</td>
   <td class="findtable" valign='top'>
        <input type="hidden" name="showall" value="" />
        <input type="checkbox" name="showall" {if $showall > ''}checked="checked"{/if} />
	{*html_checkboxes name="showall" options=$showall_options checked=$showall*}
   </td>

{*
   <td valign='bottom'>{tr}Maximum Rows{/tr}</td>
   <td class="findtable" valign='top'>
        <select name="maxrows" onchange="this.form.submit()">
          {html_options options="$max_row_options" selected="$maxrows"}
        </select>
   </td>
   <td valign='bottom'>{tr}Refresh Every{/tr}</td>
   <td class="findtable" valign='top'>
        <select name="refresh" onchange="this.form.submit()">
          {html_options options="$refresh_options" selected="$refresh"}
        </select>
   </td>
   <td valign='bottom'>{tr}Seconds{/tr}</td>
*}
</tr>
</table>
</form>
</div>

{cycle values="#dedede,#eeeeee" print=false}
<div class="simplebox">
{section name=b loop=$irc_log_rows}
{* \TODO {$irc_log_rows[b].localtime|tiki_short_time} *}
{$irc_log_rows[b].name}
{if $irc_log_rows[b].action eq 'a'}
	<div style="color:#563514;background-color:{cycle};padding:1px;">
	<tt>{$irc_log_rows[b].time} </tt><i><b>{$irc_log_rows[b].nick}</b> {$irc_log_rows[b].data}</i>
	</div>
{elseif $irc_log_rows[b].action eq 'v'}
	{if $showall > ''}
		<div style="color:#898989;background-color:{cycle};padding:1px;">
		<tt>{$irc_log_rows[b].time} {$irc_log_rows[b].data}
		</div>
	{/if}	
{else}
	<div style="background-color:{cycle};padding:1px;">
	<tt>{$irc_log_rows[b].time} </tt><b>{$irc_log_rows[b].nick}</b> {$irc_log_rows[b].data}
	</div>
{/if}
{/section}
</div>

<br clear="all" />
