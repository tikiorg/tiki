<table cellpadding="0" cellspacing="0" border="0" id="topbar">
<tr><td class="left" rowspan="2" valign="middle">
<a href="{if $http_domain}http://{$http_domain}{$http_prefix}{else}{$crumbs[0]->url}{/if}" title="{tr}Back to Homepage{/tr} {$prefs.siteTitle}" class="linkh">{$prefs.siteTitle}</a>
</td>
<td>
<table style="float:right;font-size:80%;"><tr>
{if $tiki_p_edit eq 'y'}
<td>
<form method="get" action="tiki-editpage.php">
<input type="text" size="{$size}" name="page" />
</form>
</td>
{/if}
<td>
<form method="get" action="tiki-searchindex.php">
Text Search <input type="hidden" name="where" value="wikis" />
<input type="text" name="highlight" size="14" accesskey="s" />
</form>
</td>
{if $tiki_p_view eq 'y'}
<td style="text-align:right;">
<form method="post" action="tiki-listpages.php">
&nbsp;Page Search <input type="text" name="find" />
</form>
</td>
{/if}
<td style="text-align:right;">
{if $user}
<b>{$user}</b>
<a href="tiki-logout.php">({tr}Logout{/tr})</a>
{else}
<form action="tiki-login.php" method="post">
{tr}Login{/tr}
<input type="text" name="user" id="login-user" size="12" />
<input type="password" name="pass" id="login-pass" size="12" />
<input type="hidden" name="rme" value="on"/>
<input type="submit" name="o" value="login" />
</form>
{/if}
</td></tr></table>
</td>
<td class="right"><div style="font-size:80%">
{if $prefs.feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
<a href="tiki-calendar.php" class="link">{$smarty.now|tiki_short_datetime}</a>
{else}
{$smarty.now|tiki_short_datetime}
{/if}
</div></td></tr>
<tr>
<td colspan="2">
{if $prefs.feature_phplayers eq 'y' and $prefs.feature_siteidentity eq 'y' and $prefs.feature_sitemenu eq 'y'}
<div id="plm_menu">
{phplayers id=42 type=horiz}
</div>
{/if}
</td></tr></table>

