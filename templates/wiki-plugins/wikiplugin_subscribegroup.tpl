{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_subscribegroup.tpl,v 1.1.2.1 2007-11-14 22:47:24 sylvieg Exp $ *}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}" />
{$text}
<input type="submit" name="subscribeGroup" value="{tr}OK{/tr}" />
</form>