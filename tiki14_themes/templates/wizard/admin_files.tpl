{* $Id$ *}

<img class="pull-right" src="img/icons/large/fileopen48x48.png" alt="{tr}Set up Files and File Gallery{/tr}" />
<div class="media">
	<img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Configuration Wizard{/tr}" title="{tr}Configuration Wizard{/tr}" />
	<div class="media-body">
		{tr}Set up the file gallery and attachments{/tr}. {tr}Choose to store them either in the database or in files on disk, among other options{/tr}.<br/><br/>
		<div class="row">
			<div class="col-lg-6">
				<fieldset>
					<legend>{tr}File Gallery{/tr}</legend>
					<img src="img/icons/large/file-manager.png" class="adminWizardIconright" />
					{preference name='fgal_elfinder_feature'}
					<div class="adminoptionboxchild">
						{tr}This setting makes the feature available, go to next wizard page to apply elFinder to File Galleries.
						This setting also activates jQuery, which is required for elFinder{/tr}.
						{tr}See also{/tr} <a href="http://doc.tiki.org/elFinder" target="_blank">{tr}elFinder{/tr} @ doc.tiki.org</a>
					</div>
					<br>
					{preference name='fgal_use_db'}<br>
					<em>{tr}See also{/tr} <a href="tiki-admin.php?page=fgal#content1" target="_blank">{tr}File Gallery admin panel{/tr}</a></em>
				</fieldset>
			</div>
			<div class="col-lg-6">
				<fieldset>
					<legend>{tr}Wiki Attachments{/tr}</legend>
					<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
					{preference name=feature_wiki_attachments}
					{preference name=feature_use_fgal_for_wiki_attachments}
					<br>
					<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&amp;alt=Wiki#content2" target="_blank">{tr}Wiki admin panel{/tr}</a></em>
				</fieldset>
			</div>
		</div>
	</div>
</div>
