{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_subscribegroup.tpl,v 1.1.2.3 2007-11-28 21:43:43 sylvieg Exp $ *}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}" />
{$text}
<div><input type="submit" name="subscribeGroup" value="{tr}{$action}{/tr}" /></div>
</form>