<table cellpadding="0" cellspacing="0" height="110" width="100%" border="0"><tr><td>
<div id="tiki-top_bar">
{tr}This is{/tr} TikiWiki 1.9.3 (CVS) -Sirius- &#169; 2002&#8211;2005 {tr}by the{/tr} <a href="http://tikiwiki.org" 
title="tikiwiki.org">{tr}Tiki community{/tr}</a>
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
  &#160;//&#160;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}</div>
</td></tr>
</table>

