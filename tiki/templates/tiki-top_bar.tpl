{tr}This is{/tr} Tiki v1.8 -Eta Carinae- &#169; 2002&#8211;2003 {tr}by the{/tr} <a href="http://tikiwiki.org">{tr}Tiki community{/tr}</a>
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
  &#160;//&#160;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}
