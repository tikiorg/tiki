{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{php}
global $tikilib;
$url = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
global $smarty; 
$smarty->assign('rooturl', $url);
{/php}
<form name="copyForm-{$copyIdObj}" method="post" action="{$rooturl}">
<input name="copyIdObj" type="hidden" id="copyIdObj" value="{$copyIdObj}"/> 
<input name="copyType" type="hidden" id="copyType" value="{$copyType}"/> 
<input name="copyName" type="hidden" id="copyName" value="{$copyName}"/> 
<input name="copyDesc" type="hidden" id="copyDesc" value="{$copyDesc}"/> 
<input name="copyHref" type="hidden" id="copyHref" value="{$copyHref}"/> 
<input type=image src="images/workspaces/copy.gif" alt="{tr}Copy{/tr}" border="0"/>
</form>