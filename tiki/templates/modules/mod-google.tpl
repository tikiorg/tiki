{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-google.tpl,v 1.12 2003-11-20 23:49:04 mose Exp $ *}

<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Google Search{/tr}" module_name="google"}
</div>
<div class="box-data">
<form method="get" action="http://www.google.com/search" target="Google" style="margin-bottom:2px;">
<input type="hidden" name="hl" value="en"/>
<input type="hidden" name="oe" value="UTF-8"/>
<input type="hidden" name="ie" value="UTF-8"/>
<input type="hidden" name="btnG" value="Google Search"/>
<input name="googles" type="image" width='16' height='16' src="img/googleg.gif" border="0" alt="Google" align="left" vspace="0" hspace="4"/>
<input type="text" name="q" size="12"  maxlength="100" />
{if $http_domain ne ''}
<input type="hidden" name="domains" value="{$http_domain}" /><br />
<input type="radio" name="sitesearch" value="{$http_domain}" checked>{$http_domain}</input><br />
<input type="radio" name="sitesearch" value="">WWW</input>
{/if}
</form>
</div>
</div>
