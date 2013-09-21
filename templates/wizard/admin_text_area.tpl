{* $Id$ *}

<h1>{tr}Set up your Text Area{/tr}</h1>

{tr}Set up your text area environment (Editing and Plugins){/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Text Area{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}General settings{/tr}</legend>
	<img src="img/icons/large/editing48x48.png" style="float:right" width="32px"/>	
	{preference name=feature_fullscreen}
	{preference name=feature_syntax_highlighter}
	{preference name=feature_syntax_highlighter_theme}
	{tr}See also{/tr} <a href="tiki-admin.php?page=textarea&alt=Editing+and+Plugins#content1" target="_blank">{tr}Editing and plugins admin panel{/tr}</a>
</fieldset>
<fieldset>
	<legend>{tr}Plugin preferences{/tr}</legend>
	{preference name=wikipluginprefs_pending_notification}
	<b>{tr}Some recommended plugins{/tr}:</b><br> 
	{preference name=wikiplugin_convene}
	{preference name=wikiplugin_slider}
	{preference name=wikiplugin_slideshow}
	{preference name=wikiplugin_wysiwyg}
	
	{tr}See also{/tr} <a href="tiki-admin.php?page=textarea&alt=Editing+and+Plugins#content2" target="_blank">{tr}Editing and plugins admin panel{/tr}</a>
	
</fieldset>

</div>
