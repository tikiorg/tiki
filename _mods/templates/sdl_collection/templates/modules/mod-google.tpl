{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-google.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{tikimodule title="{tr}Google Search{/tr}" name="google"}
<form method="get" action="http://www.google.com/search" target="Google" style="margin-bottom:2px;">
  <input type="hidden" name="hl" value="en"/>
  <input type="hidden" name="oe" value="UTF-8"/>
  <input type="hidden" name="ie" value="UTF-8"/>
  <input type="hidden" name="btnG" value="Google Search"/>
  <input name="googles" type="image" width='16' height='16' src="img/googleg.gif" border="0" alt="Google" align="left" vspace="0" hspace="4"/>
  <input type="text" name="q" size="12"  maxlength="100" />
  <!--{if $http_domain ne ''}
    <input type="hidden" name="domains" value="{$http_domain}" /><br />
    <input type="radio" name="sitesearch" value="{$http_domain}">{$http_domain}</input><br />-->
    <input type="submit" name="sitesearch" value="Go" checked><!--www--></input>
  {/if}
</form>
{/tikimodule}
