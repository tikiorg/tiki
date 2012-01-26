{* $Id: wikiplugin_shopperinfo.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
<form method="post" action="{query _type=relative _keepall=y}" style="display: inline;">
	{section name=v loop=$values}
	{$values[v].label|escape}: <input type="text" size="20" name="{$values[v].name}" value="{$values[v].current}" />
	<br />
	{/section}
	<input type="submit" name="shopperinfo" value="{tr}Submit{/tr}"/>
</form>

