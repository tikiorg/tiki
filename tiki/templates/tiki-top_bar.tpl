{tr}This is{/tr} Tiki v1.7.1 -Eta Carinae- (c)2002-2003 {tr}by the{/tr} 
<a href="http://tikiwiki.org" target="_blank">Tiki community</a> 
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
{$smarty.now|tiki_short_datetime}
{/if}
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
&nbsp;//&nbsp;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}
