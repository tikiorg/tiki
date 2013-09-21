{* $Id$ *}

<h1>{tr}Set up language{/tr}</h1>

<div style="float:left; width:60px"><img src="img/icons/large/i18n.png" alt="{tr}Set up the language{/tr}"></div>
{tr}Select you site language{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Language{/tr}</legend>

	{preference name=language}
	<br>
	{preference name=feature_multilingual visible="always"}
	{preference name=lang_use_db}

</fieldset>

</div>
