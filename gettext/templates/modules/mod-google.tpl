{tikimodule error=$module_params.error title=$tpl_module_title name="google" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form method="get" action="http://www.google.com/search" target="Google" style="margin-bottom:2px;">
  <input type="hidden" name="hl" value="en"/>
  <input type="hidden" name="oe" value="UTF-8"/>
  <input type="hidden" name="ie" value="UTF-8"/>
  <input type="hidden" name="btnG" value="Google Search"/>
  <input name="googles" type="image" src="img/googleg.gif" alt="Google" align="left" />&nbsp;
  <input type="text" name="q" size="12"  maxlength="100" />
  {if $url_host ne ''}
    <input type="hidden" name="domains" value="{$url_host}" /><br />
    <input type="radio" name="sitesearch" value="{$url_host}" checked="checked" />{$url_host}<br />
    <input type="radio" name="sitesearch" value="" />WWW
  {/if}
</form>
{/tikimodule}
