<div id="tiki-top">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="left">
<a href="http://{$http_domain}" title="{tr}back to homepage{/tr} {$siteTitle}" class="linkh">{$siteTitle}</a>
</td>
<td class="center">
{if $user}
{include file="tiki-mytiki_bar.tpl"}
{else}
{tr}Please{/tr} <a href="tiki-login_scr.php" class="link">{tr}log in{/tr}</a> {tr}to access full functionalities{/tr}
{/if}
</td>
<td class="right"><div>
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<a href="tiki-calendar.php" class="link">{$smarty.now|tiki_short_datetime}</a>
{else}
{$smarty.now|tiki_short_datetime}
{/if}
</div></td></tr></table>
</div>
