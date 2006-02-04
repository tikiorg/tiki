{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'} <a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>{else} {$smarty.now|tiki_short_datetime}{/if} {if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}  &#160;//&#160;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}

