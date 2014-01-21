{* $Id$ *}
{strip}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}">
<input type="hidden" name="iSubscribeGroup" value="{$iSubscribeGroup}">
{$text|escape}
<div><input type="submit" class="btn btn-default" name="subscribeGroup" value="{tr}{$action}{/tr}"></div>
</form>
{/strip}