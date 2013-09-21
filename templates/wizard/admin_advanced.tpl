{* $Id$ *}

<h1>{tr}Set up some advanced options{/tr}</h1>

<div style="float:left; width:60px"><img src="img/icons/large/areas48x48.png" alt="{tr}Set up Workspaces & Areas{/tr}"></div>
{tr}If you are an experience tiki site administrator, you might like to know and maybe even try some of these advanced features below. They might be very useful for you, for instance, if you plan to periodically create a similar set of Tiki objects for different groups of users with a similar set permissions each time{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Workspaces{/tr}</legend>
	{preference name=workspace_ui}
	{tr}See also{/tr} <a href="https://doc.tiki.org/Workspaces UI" target="_blank">{tr}Workspaces UI in doc.tiki.org{/tr}</a>
</fieldset>
<fieldset>
	<legend>{tr}Dependencies{/tr}</legend>
	{tr}Enable using the same wiki page name in different contexts{/tr}. {tr}E.g. ns1:_:MyPage and ns2:_:MyPage{/tr}.
	{preference name=namespace_enabled}
	{preference name=feature_perspective}
	{preference name=feature_categories}
	
	{tr}See also{/tr} <a href="tiki-admin.php?page=workspace" target="_blank">{tr}Workspaces & Areas admin panel{/tr}</a>
	
</fieldset>

</div>
