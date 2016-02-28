{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {tr}Set up the text area environment (Editing and Plugins){/tr}.</br></br>
	<div class="media">
        {icon name="admin_textarea" size=3 iclass="adminWizardIconright"}
		<fieldset>
			<legend>{tr}General settings{/tr}</legend>
			<div class="admin clearfix featurelist">
				{preference name=feature_fullscreen}
				{preference name=wiki_edit_plugin}
				{preference name=wiki_edit_icons_toggle}
				{preference name=wikipluginprefs_pending_notification}
				{if $isRTL eq false and $isHtmlMode neq true}		{* Disable Codemirror for RTL languages. It doesn't work. *}
					{preference name=feature_syntax_highlighter}
					{preference name=feature_syntax_highlighter_theme}
				{/if}
			</div>
			<br>
			<em>{tr}See also{/tr} <a href="tiki-admin.php?page=textarea&amp;alt=Editing+and+Plugins#content1" target="_blank">{tr}Editing and plugins admin panel{/tr}</a></em>
		</fieldset>
	</div>
</div>
