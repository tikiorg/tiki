<span style="position:absolute;right:4px;width:151px;border-bottom:1px solid #504030;border-left:1px solid #000000;" class="link">{$smarty.now|tiki_short_datetime}</span> 
<a href="http://{$http_domain}" title="{tr}back to homepage{/tr} {$siteTitle}"><img src="styles/mose/topimage172x14.png" width="172" height="14" alt="{$siteTitle}" vspace="0" hspace="0" border="0" align="top"></a>
{if $user}
{include file="tiki-mytiki_bar.tpl"}
{else}
<span style="position:absolute;left:195px;top:0;" id="tiki-top">
{tr}Please login to access full functionnalities{/tr}
</span>
{/if}

