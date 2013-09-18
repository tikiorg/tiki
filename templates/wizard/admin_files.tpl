{* $Id$ *}

<h1>{tr}Set up Files and File Gallery{/tr}</h1>

{tr}Set up your file gallery and attachments{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}File Gallery{/tr}</legend>
	<img src="img/icons/large/file-manager.png" style="float:right" />	
	{preference name='fgal_use_db'}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=fgal#content1" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
	<legend>{tr}Wiki Attachments{/tr}</legend>
	<img src="img/icons/large/wikipages.png" style="float:right" />	
	{preference name=feature_wiki_attachments}
	{preference name=feature_use_fgal_for_wiki_attachments}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content2" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
</div>
