  <a href="http://tikiwiki.org" 
title="&#169; 2002&#8211;2006 {tr}by the {/tr}{tr}Tiki community{/tr} - tikiwiki.org">Tiki CMS/Groupware</a><br />
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}