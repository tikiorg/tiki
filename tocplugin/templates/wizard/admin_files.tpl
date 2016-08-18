{* $Id$ *}

<div class="media">
<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
	<i class="fa fa-gear fa-stack-2x"></i>
	<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
</span>
    {icon name="admin_fgal" size=3 iclass="pull-right"}
    {tr}Set up the file gallery and attachments{/tr}. {tr}Choose to store them either in the database or in files on disk, among other options{/tr}.<br/><br/>
	<div class="media-body">
		<div class="row">
			<div class="col-lg-6">
				<fieldset>
					<legend>{tr}File Gallery{/tr}</legend>
					{icon name="admin_fgal" size=2 iclass="adminWizardIconright"}
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
					{icon name="admin_wiki" size=2 iclass="adminWizardIconright"}
					{preference name=feature_wiki_attachments}
					{preference name=feature_use_fgal_for_wiki_attachments}
					<br>
					<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&amp;alt=Wiki#content2" target="_blank">{tr}Wiki admin panel{/tr}</a></em>
				</fieldset>
			</div>
		</div>
	</div>
</div>
