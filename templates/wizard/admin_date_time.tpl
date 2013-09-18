{* $Id$ *}

<h1>{tr}Set up Date and Time{/tr}</h1>

{tr}Set up your time-zone and how dates and times are displayed{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Date and Time setup{/tr}</legend>
	<img src="img/icons/large/icon-configuration.png" style="float:right" />	
	{preference name=server_timezone}
	{preference name=users_prefs_display_timezone}
	<br>
	{preference name=display_field_order}
	{preference name=users_prefs_display_12hr_clock}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=general&alt=General#content4" target="_blank">{tr}Date and Time admin panel{/tr}</a>
</fieldset>
<br>
</div>
