{* $Id *}

<h1>{tr}Set up Files and File Gallery{/tr}</h1>

{tr}Set up your file gallery and external file paths{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}File Gallery storage{/tr}</legend>
	{preference name='fgal_use_db'}
	If stored in a directory, you need specify the path ...
	{preference name='fgal_use_dir'}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=fgal#content1" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
	<legend>{tr}Attachment storage{/tr}</legend>
	{preference name=feature_wiki_attachments}
	If attachments are enabled ...
	{preference name=w_use_db}
	{preference name=w_use_dir}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content2" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
</div>
