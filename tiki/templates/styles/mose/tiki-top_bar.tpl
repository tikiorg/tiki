<table cellpadding="0" cellspacing="0" border="0" id="tiki-top">
<tr><td class="left">
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
<a href="javascript:toggle('debugconsole');" style="float:right;">&nbsp;//&nbsp;{tr}debug{/tr}</a>
{/if}
<a href="http://{$http_domain}" title="{tr}back to homepage{/tr} {$siteTitle}" class="linkh">{$siteTitle}</a>
</td>
<td class="center">
{if $user}
{include file="tiki-mytiki_bar.tpl"}
{else}
&nbsp;
{tr}Please{/tr} <a href="tiki-login_scr.php" class="linkmenu">{tr}log in{/tr}</a> {tr}to access full functionalities{/tr}
{/if}
&nbsp;
</td>
<td class="right">
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<a href="tiki-calendar.php">{$smarty.now|tiki_short_datetime}</a>
{else}
{$smarty.now|tiki_short_datetime}
{/if}
</td></tr></table>

