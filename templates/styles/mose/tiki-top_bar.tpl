<div id="tiki-top">
<table cellpadding="0" cellspacing="0" border="0" id="tiki-top" >
<tr><td align="center" width="180" bgcolor="#000000">
<a href="http://{$http_domain}" title="{tr}back to homepage{/tr} {$siteTitle}" class="linkh">{$siteTitle}</a>
</td>
<td class="center">
{if $user}
{include file="tiki-mytiki_bar.tpl"}
{else}
{tr}Please{/tr} <a href="tiki-login_scr.php" class="link">{tr}log in{/tr}</a> {tr}to access full functionalities{/tr}
{/if}
</td>
<td align="right" width="180" bgcolor="#000000">
{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<a href="tiki-calendar.php" class="link">{$smarty.now|tiki_short_datetime}</a>
{else}
{$smarty.now|tiki_short_datetime}
{/if}
</td></tr></table>
</div>
