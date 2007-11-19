{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_subscribegroup.tpl,v 1.1.2.2 2007-11-19 23:23:03 sylvieg Exp $ *}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}" />
{$text}
<input type="submit" name="subscribeGroup" value="{tr}{$action}{/tr}" />
</form>