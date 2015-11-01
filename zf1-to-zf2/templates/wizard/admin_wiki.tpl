{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	<div class="media-content">
        {tr}Set up the Wiki environment{/tr}</br></br></br>
        {icon name="file-text-o" size=3 iclass="adminWizardIconright"}
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
				{preference name=flaggedrev_approval}
				<div id="flaggedrev_approval_childcontainer">
					{if $prefs['feature_categories'] eq 'y'}
						{preference name=flaggedrev_approval_categories}
					{else}
						{remarksbox type="info" title="{tr}Info{/tr}"}
							{tr}Once you have the feature '<strong>Categories</strong>' enabled, you will need to define some content categories, and indicate which ones require revision approval for their wiki pages{/tr}.
							<br><br/>
							{tr}You will be able to set the category ids here when you come back with Categories enabled, or at the corresponding <a href="tiki-admin.php?page=wiki&cookietab=3" target="_blank">Control Panel</a> with the '<em>Advanced</em>' features shown in the Preference Filters{/tr}.
						{/remarksbox}
					{/if}
				</div>
			</div>
			<br><br>
			<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&amp;alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a></em>

		</fieldset>
	</div>
</div>
