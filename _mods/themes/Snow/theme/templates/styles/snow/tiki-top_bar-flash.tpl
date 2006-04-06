<table cellpadding="0" cellspacing="0" width="100%" border="0" align="center">
<td>
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
 codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
 WIDTH="734" HEIGHT="95" id="snowfall" ALIGN="center">
 <PARAM NAME=movie VALUE="styles/snow/Flash/snowfall.swf"> <PARAM NAME=loop VALUE=false> <PARAM NAME=menu VALUE=false> <PARAM NAME=quality VALUE=high><PARAM NAME=scale VALUE=exactfit> <PARAM NAME=bgcolor VALUE=#AFC0DC> <EMBED src="styles/snow/Flash/snowfall.swf" loop=false menu=false quality=high scale=exactfit  bgcolor=#AFC0DC WIDTH="734" HEIGHT="95" NAME="snowfall" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
</OBJECT>
</td>
</table>

{tr}This is{/tr} TikiWiki 1.9.3 (CVS) -Sirius- &#169; 2002&#8211;2006 {tr}by the{/tr} <a href="http://tikiwiki.org" 
title="tikiwiki.org">{tr}Tiki community{/tr}</a>
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
  &#160;//&#160;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}

