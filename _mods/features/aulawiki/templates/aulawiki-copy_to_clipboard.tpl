{php}
global $tikilib;
$url = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
global $smarty; 
$smarty->assign('rooturl', $url);
{/php}
<form name="copyForm-{$copyIdObj}" method="post" action="{$rooturl}">
<input name="copyIdObj" type="hidden" id="copyIdObj" value="{$copyIdObj}"> 
<input name="copyType" type="hidden" id="copyType" value="{$copyType}"> 
<input name="copyName" type="hidden" id="copyName" value="{$copyName}"> 
<input name="copyDesc" type="hidden" id="copyDesc" value="{$copyDesc}"> 
<input name="copyHref" type="hidden" id="copyHref" value="{$copyHref}"> 
</form>
 <img src="images/aulawiki/copy.gif" onclick="document['copyForm-{$copyIdObj}'].submit();">
