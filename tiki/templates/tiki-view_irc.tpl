{* $CVSHeader$
Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
All Rights Reserved. See copyright.txt for details and a complete list of authors.
Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
*}

<a class="pagetitle" href="tiki-view_irc.php">
{tr}IRC log{/tr} {$irc_log_channel} {$irc_log_time|tiki_long_date}
</a>
<br/><br/>
<div>
<table>
<tr>
   <td valign='bottom'>{tr}Select{/tr}</td>
   <td class="findtable" valign='top'>
     <form method="get" action="tiki-view_irc.php">
        <select name="log" onchange="this.form.submit()">
        {html_options options="$irc_log_options" selected="$irc_log_selected"}
        </select>
       <input type="submit" value="{tr}display{/tr}" />
     </form>
   </td>
</tr>
</table>
</div>

{cycle values="#dedede,#eeeeee" print=false}
<div class="simplebox">
{section name=b loop=$irc_log_rows}
{* \TODO {$irc_log_rows[b].localtime|tiki_short_time} *}
{if $irc_log_rows[b].action eq 'a'}
	<div style="color:#563514;background-color:{cycle};padding:1px;">
	<tt>{$irc_log_rows[b].time} : </tt><i><b>{$irc_log_rows[b].nick}</b> {$irc_log_rows[b].data}</i>
	</div>
{elseif $irc_log_rows[b].action eq 'v'}
	<div style="color:#898989;background-color:{cycle};padding:1px;">
	<tt>{$irc_log_rows[b].time} : {$irc_log_rows[b].data}
	</div>
{else}
	<div style="background-color:{cycle};padding:1px;">
	<tt>{$irc_log_rows[b].time} : </tt><b>{$irc_log_rows[b].nick}</b> {$irc_log_rows[b].data}
	</div>
{/if}
{/section}
</div>

<br clear="all" />
