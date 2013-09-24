{* $Id$ *}

<h1>{tr}Set up Files and File Gallery{/tr}</h1>

{tr}Set up your file gallery and attachments{/tr}. {tr}Choose to store them either in the database or in files on disk, among other options{/tr}.
<div class="adminWizardIconleft"><img src="img/icons/large/file-manager48x48.png" alt="{tr}Set up Files and File Gallery{/tr}" /></div>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}File Gallery{/tr}</legend>
	{preference name='fgal_elfinder_feature'}
	{tr}Depends on jQuery UI, which will be activated, if ElFinder is used{/tr}.<br>
	{tr}See also{/tr} <a href="http://doc.tiki.org/elFinder" target="_blank">{tr}ElFinder{/tr} @ doc.tiki.org</a><br>
	<br>
	{preference name='fgal_use_db'}
	{tr}See also{/tr} <a href="tiki-admin.php?page=fgal#content1" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
	<legend>{tr}Wiki Attachments{/tr}</legend>
	<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
	{preference name=feature_wiki_attachments}
	{preference name=feature_use_fgal_for_wiki_attachments}
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content2" target="_blank">{tr}File Gallery admin panel{/tr}</a>
</fieldset>
<br>
</div>
