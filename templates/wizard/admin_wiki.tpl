{* $Id$ *}

{tr}Set up the Wiki environment{/tr}
<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/wikipages48x48.png" alt="{tr}Set up the Wiki environment{/tr}" /></div>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Wiki environment{/tr}</legend>
	<div class="admin clearfix featurelist">
	{preference name=feature_categories}
	{preference name=wiki_auto_toc}
	<div class="adminoptionboxchild">
		{tr}See also{/tr} <a href="https://doc.tiki.org/Category" target="_blank">{tr}Category{/tr} @ doc.tiki.org</a>
	</div>
	{preference name=feature_jcapture}
	{preference name=feature_wiki_structure}
	<div class="adminoptionboxchild">
		{tr}Look for the <img src="img/icons/camera.png" /> icon in the editor toolbar{/tr}. {tr}Requires Java{/tr}.<br/><a href="https://www.java.com/verify/" target="_blank">{tr}Verify your Java installation{/tr}</a>.<br>
	</div>
	</div>
	<br><br>
	<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&amp;alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a></em>
</fieldset>

</div>
