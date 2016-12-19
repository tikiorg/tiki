{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {tr}Choose your desired settings below{/tr}</br></br>
	<div class="media-body">
		<p>
			{if isset($promptElFinder) AND $promptElFinder eq 'y'}
				<div>
					<fieldset>
						<legend>{tr}elFinder{/tr}</legend>
                        {icon name="admin_fgal" size=2 iclass="pull-right"}
						<input type="checkbox" name="useElFinderAsDefault" {if !isset($useElFinderAsDefault) or $useElFinderAsDefault eq true}checked='checked'{/if} /> {tr}Set elFinder as the default file gallery viewer{/tr}.
						<div class="adminoptionboxchild">
							{tr}See also{/tr} <a href="http://doc.tiki.org/elFinder" target="_blank">{tr}elFinder{/tr} @ doc.tiki.org</a>
						</div>
						<br>
					</fieldset>
				</div>
			{/if}
			{if isset($promptFileGalleryStorage) AND $promptFileGalleryStorage eq 'y'}
				<div>
					<fieldset>
						<img src="img/icons/large/file-manager.png" class="adminWizardIconright" />
						<legend>{tr}File Gallery storage{/tr}</legend>
						{preference name='fgal_use_dir'}
					</fieldset>
				</div>
			{/if}
			{if isset($promptAttachmentStorage) AND $promptAttachmentStorage eq 'y'}
				<div>
					<fieldset>
						<legend>{tr}Attachment storage{/tr}</legend>
						<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
						{preference name=w_use_db}
						{preference name=w_use_dir}
					</fieldset>
				</div>
			{/if}
		</p>
	</div>
</div>
