{* $Id: wikiplugin_subscribegroup.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{strip}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}" />
<input type="hidden" name="iSubscribeGroup" value="{$iSubscribeGroup}" />
{$text|escape}
<div><input type="submit" name="subscribeGroup" value="{tr}{$action}{/tr}" /></div>
</form>
{/strip}